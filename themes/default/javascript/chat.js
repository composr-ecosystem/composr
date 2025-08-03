(function ($cms, $util, $dom) {

    'use strict';
    // Constants
    window.MESSAGE_CHECK_INTERVAL = Math.max(3000, parseInt($cms.configOption('chat_message_check_interval')));
    window.TRANSITORY_ALERT_TIME = parseInt($cms.configOption('chat_transitory_alert_time'));
    window.LOGS_DOWNLOAD_INTERVAL = 3000;

    // Tracking variables
    window.lastMessageId = -1;
    window.lastTimestamp = 0;
    window.lastEventId = -1;
    window.messageChecking = false;
    window.noImHtml = '';
    window.textColour = null;
    window.openedPopups = {};
    window.loadFromRoomId = null;
    window.alreadyReceivedRoomInvites = {};
    window.alreadyReceivedContactAlert = {};
    window.instantGo = false;
    window.isShutdown = false;
    window.allConversations = {};

    $cms.views.ChatRoomScreen = ChatRoomScreen;
    /**
     * @memberof $cms.views
     * @class
     * @extends $cms.View
     */
    function ChatRoomScreen(params) {
        ChatRoomScreen.base(this, 'constructor', arguments);
        this.chatroomId = strVal(params.chatroomId);

        // Used by this.checkChatOptions()
        this.chatOptionsFormLastValid = null;

        $dom.load.then(function () {
            chatLoad(params.chatroomId);
        });
    }

    $util.inherits(ChatRoomScreen, $cms.View, /**@lends ChatRoomScreen#*/{
        events: function () {
            return {
                'click .js-btn-toggle-chat-comcode-panel': 'toggleChatPanel',
                'click select.js-select-click-font-change': 'fontChange',
                'change select.js-select-change-font-change': 'fontChange',
                'click .js-check-chat-options': 'checkChatOptions',
                'click .js-click-post-chat-message': 'postChatMessage',
                'keypress .js-keypress-enter-post-chat': 'enterChatMessage',
                'change .js-change-input-text-color': 'changeInputTextColor',
                'click .js-click-open-emoticon-chooser-window': 'openEmoticonChooserWindow'
            };
        },

        toggleChatPanel: function () {
            $cms.ui.toggleableTray($dom.$('#chat-comcode-panel'));
        },

        fontChange: function (e, selectEl) {
            this.$('#font').value = selectEl.value;
            this.$('#post').style.fontFamily = selectEl.value;
            $cms.ui.manageScrollHeight(this.$('#post'));
        },

        checkChatOptions: function (e, button) {
            if ($dom.isCancelledSubmit(e)) {
                return;
            }

            var form = button.form;

            if (!form.elements['text_colour'].value.match(/^#[0-9A-F][0-9A-F][0-9A-F]([0-9A-F][0-9A-F][0-9A-F])?$/i)) {
                $cms.ui.alert('{!chat:BAD_HTML_COLOUR;^}');
                $dom.cancelSubmit(e);
                return;
            }

            if (this.chatOptionsFormLastValid && (this.chatOptionsFormLastValid.getTime() === $cms.form.lastChangeTime(form).getTime())) {
                return;
            }

            e.preventDefault();

            var that = this;
            var promise = $cms.form.checkForm(e, form, false, []).then(function (valid) {
                if (valid) {
                    that.chatOptionsFormLastValid = $cms.form.lastChangeTime(form);
                }

                return valid;
            });

            $dom.awaitValidationPromiseAndSubmitForm(e, promise, null, form);
        },

        postChatMessage: function (e) {
            chatPost(e, this.chatroomId, 'post', this.$('#font_name').value, this.$('#text_colour').value);
        },

        enterChatMessage: function (e) {
            if ($dom.keyPressed(e, 'Enter')) {
                this.postChatMessage(e);
            }
        },

        changeInputTextColor: function (e, input) {
            if (input.value && /^#[0-9A-F]{3}([0-9A-F]{3})?$/i.test(input.value)) {
                input.style.color = input.value;
                this.$('#colour').value = input.value;
            }
        },

        openEmoticonChooserWindow: function () {
            $cms.ui.open($util.rel($cms.maintainThemeInLink('{$FIND_SCRIPT_NOHTTP;,emoticons}?field_name=post' + $cms.keep())), 'emoticon_chooser', 'width=300,height=320,status=no,resizable=yes,scrollbars=no');
        }
    });

    $cms.templates.chatSound = function chatSound(params) { // Prepares chat sounds
        if (window.preparedChatSounds) {
            return;
        }
        window.preparedChatSounds = true;

        window.soundManager.setup({
            url: $util.rel('data'),
            debugMode: false,
            onready: function () {
                var soundEffects = params.soundEffects, i;

                for (i in soundEffects) {
                    window.soundManager.createSound(soundEffects[i].key, soundEffects[i].value);
                }
            }
        });
    };

    $cms.templates.chatFriends = function chatFriends(params, container) {
        var friends = arrVal(params.friends), friend;

        for (var i = 0; i < friends.length; i++) {
            friend = friends[i];

            if (friend.onlineText !== '{!chat:ACTIVE;^}') {
                document.getElementById('friend-img-' + friend.memberId).classList.remove('friend-active');
                document.getElementById('friend-img-' + friend.memberId).classList.add('friend-inactive');
            }
        }

        $dom.on(container, 'click', '.js-click-start-friend-im', function (e, link) {
            var memberId = strVal(link.dataset.tpMemberId);
            e.preventDefault();

            startIm(memberId, true);
        });
    };

    $cms.templates.chatLobbyImArea = function chatLobbyImArea(params, container) {
        var chatroomId = strVal(params.chatroomId);

        $dom.load.then(function () {
            try {
                $dom.$('#post_' + chatroomId).focus();
            } catch (e) {}
            $dom.$('#post_' + chatroomId).value = $cms.readCookie('last_chat_msg_' + chatroomId, 'PERSONALIZATION');
        });

        $dom.on(container, 'click', '.js-click-chatroom-chat-post', function (e) {
            chatPost(e, chatroomId, 'post_' + chatroomId, '', '');
        });

        $dom.on(container, 'click', '.js-click-open-chat-emoticons-popup', function () {
            var openFunc = (window.opener ? window.open : $cms.ui.open),
                popupUrl = strVal(params.emoticonsPopupUrl);

            openFunc($util.rel($cms.maintainThemeInLink(popupUrl)), 'emoticon_chooser', 'width=300,height=320,status=no,resizable=yes,scrollbars=no');
        });

        $dom.on(container, 'click', '.js-click-close-chat-conversation', function () {
            closeChatConversation(chatroomId);
        });

        $dom.on(container, 'keypress', '.js-keypress-eat-enter', function (e) {
            if ($dom.keyPressed(e, 'Enter')) {
                e.preventDefault();
            }
        });

        $dom.on(container, 'keyup', '.js-keyup-textarea-chat-post', function (e, textarea) {
            if (!$cms.isMobile()) {
                $cms.ui.manageScrollHeight(textarea);
            }

            if ($dom.keyPressed(e, 'Enter')) {
                $cms.setCookie('last_chat_msg_' + chatroomId, '', 'PERSONALIZATION');
                chatPost(e, chatroomId, 'post_' + chatroomId, '', '');
                e.preventDefault();
            } else {
                $cms.setCookie('last_chat_msg_' + chatroomId, textarea.value, 'PERSONALIZATION');
            }
        });
    };

    $cms.templates.chatLobbyScreen = function chatLobbyScreen(params, container) {
        if ($cms.isGuest()) {
            return;
        }

        window.imAreaTemplate = params.imAreaTemplate;
        window.imParticipantTemplate = params.imParticipantTemplate;
        window.topWindow = window;

        window.loadFromRoomId = -1;
        if ((window.chatCheck)) {
            chatCheck(true, 0);
        } else {
            setTimeout(beginImChatting, 500);
        }

        $dom.on(container, 'click', '.js-click-btn-im-invite-ticked-people', function (e, btn) {
            var people = getTickedPeople(btn.form);
            if (people) {
                inviteIm(people);
            }
        });

        $dom.on(container, 'click', '.js-click-btn-im-start-ticked-people', function (e, btn) {
            var people = getTickedPeople(btn.form);
            if (people) {
                startIm(people);
            }
        });

        $dom.on(container, 'click', '.js-click-btn-dump-friends-confirm', function (e, btn) {
            var people = getTickedPeople(btn.form);
            if (people) {
                $cms.ui.confirm('{!Q_SURE=;}', function (result) {
                    if (result) {
                        $cms.ui.disableButton(btn);
                        btn.form.submit();
                    }
                });
            }
        });

        $dom.on(container, 'keyup', '.js-keyup-input-update-ajax-member-list', function (e, btn) {
            $cms.form.updateAjaxMemberList(btn, null, false, e);
        });

        $dom.on(container, 'click', '.js-add-friend', function (e, btn) {
            $cms.loadSnippet('im_friends_rejig&member_id=' + params.memberId, 'add=' + encodeURIComponent(btn.form.elements['friend_username'].value)).then(function (html) {
                $dom.html('#friends-wrap', html);
                btn.form.elements['friend_username'].value = '';
            });
            e.preventDefault();
        });

        function beginImChatting() {
            window.loadFromRoomId = -1;
            chatCheck(true, 0);
        }
    };

    $cms.templates.chatModerateScreen = function chatModerateScreen(params, container) {
        $dom.on(container, 'click', '.js-click-btn-delete-marked-posts', function (e, btn) {
            if ($cms.form.addFormMarkedPosts(btn.form, 'del_')) {
                $cms.ui.disableButton(btn);
            } else {
                $cms.ui.alert('{!NOTHING_SELECTED=;}');
                e.preventDefault();
            }
        });
    };

    $cms.templates.chatLobbyImParticipant = function chatLobbyImParticipant(params, container) {
        $dom.on(container, 'click', '.js-click-hide-self', function (e, clicked) {
            $dom.hide(clicked);
        });
    };

    $cms.templates.chatSiteWideImPopup = function () {
        window.detectIfChatWindowClosedChecker = setInterval(function () {
            detectIfChatWindowClosed();
        }, 5);

        function detectIfChatWindowClosed(dieOnLost, becomeAutonomousOnLost) {
            var lostConnection = false;
            try {
                if (!window.opener || !window.opener.document) {
                    lostConnection = true;
                } else {
                    var roomId = findCurrentImRoom();
                    if (window.opener.openedPopups['room_' + roomId] === undefined) {
                        var chatLobbyConvosTabs = window.opener.document.getElementById('chat-lobby-convos-tabs');
                        if (chatLobbyConvosTabs) {// Now in the chat lobby, consider this a confirmed loss, because we don't want duplicate IM spaces
                            dieOnLost = true;
                            becomeAutonomousOnLost = false;
                            lostConnection = true;
                        } else {
                            window.opener.openedPopups['room_' + roomId] = window; // Reattach, presumably a navigation has happened

                            if ((window.alreadyAutonomous !== undefined) && (window.alreadyAutonomous)) {// Losing autonomity again?
                                window.topWindow = window.opener;
                                chatCheck(false, window.lastMessageId, window.lastEventId);
                                window.alreadyAutonomous = false;
                            }

                            window.opener.$util.inform('Reattaching chat window to re-navigated top-level window.');
                        }
                    }
                }
            } catch (err) {
                lostConnection = true;
            }

            if (lostConnection) {
                if (dieOnLost === undefined) {
                    dieOnLost = false;
                }
                if (becomeAutonomousOnLost === undefined) {
                    becomeAutonomousOnLost = false;
                }

                if (becomeAutonomousOnLost) { // Becoming autonomous means allowing to work with a top-level window
                    chatWindowBecomeAutonomous();
                } else if (dieOnLost) {
                    window.isShutdown = true;
                    window.onbeforeunload = null;
                    window.close();
                } else {
                    if ((window.alreadyAutonomous === undefined) || (!window.alreadyAutonomous)) {
                        setTimeout(function () {
                            detectIfChatWindowClosed(false, true);
                        }, 3000); // If connection still lost after this time then kill the window
                    }
                }
            }

            function chatWindowBecomeAutonomous() {
                if ((window.alreadyAutonomous === undefined) || (!window.alreadyAutonomous)) {
                    window.topWindow = window;
                    chatCheck(false, window.lastMessageId, window.lastEventId);
                    window.alreadyAutonomous = true;
                }
            }
        }
    };

    $cms.templates.blockMainFriendsList = function (params, container) {
        $dom.on(container, 'keyup', '.js-input-friends-search', function (e, input) {
            $cms.form.updateAjaxSearchList(input, e);
        });
    };

    $cms.templates.blockSideShoutbox = function blockSideShoutbox(params, container) {
        $dom.on(container, 'click', '.js-onclick-check-message-not-blank', function (e) {
            if (!$cms.form.checkFieldForBlankness(document.getElementById('shoutbox-message'))) {
                e.preventDefault();
            }
        });
    };

    $cms.templates.chatSiteWideIm = function (params) {
        if (!params.matched) {
            return;
        }

        window.imAreaTemplate = params.imAreaTemplate;
        window.imParticipantTemplate = params.imParticipantTemplate;
        window.topWindow = window;
        window.lobbyLink = params.lobbyLink;
        window.participants = '';

        if (!window.loadFromRoomId) { // Only if not in chat lobby or chatroom, so as to avoid conflicts
            beginImChatting();
        }

        function beginImChatting() {
            window.loadFromRoomId = -1;
            chatCheck(true, 0);
        }
    };

    $cms.templates.chatSetEffectsSettingBlock = function (params, container) {
        var effects = params.effects || {};

        for (var effectName in effects) {
            var effect = effects[effectName];

            if (!$cms.isHttpauthLogin()) {
                var name = 'upload_' + effect.key;

                if (effect.memberId) {
                    name += '_' + effect.memberId;
                }

                if ($cms.configOption('complex_uploader')) {
                    window.$plupload.preinitFileInput('chat_effect_settings', name, null, 'mp3');
                }
            }
        }

        $dom.on(container, 'click', '.js-click-require-sound-selection', function (e, clicked) {
            var select = $dom.$('#' + clicked.dataset.tpSelectId);
            if (select.value === '') {
                $cms.ui.alert('{!chat:PLEASE_SELECT_SOUND;}');
            } else {
                playSoundUrl(select.value);
            }
        });
    };

    function playSoundUrl(url) { // Used for testing different sounds
        var baseUrl = $util.rel((!url.includes('data_custom/') && !url.includes('uploads/')) ? $cms.getBaseUrl() : $cms.getCustomBaseUrl());
        var soundObject = window.soundManager.createSound({url: baseUrl + '/' + url});
        if (soundObject) {
            soundObject.play();
        }
    }

    function playChatSound(sId, forMember) {
        var playSound = document.getElementById('play_sound');

        if ((playSound) && (!playSound.checked)) {
            return;
        }

        if (forMember) {
            var override = window.topWindow.soundManager.getSoundById(sId + '_' + forMember, true);
            if (override) {
                sId = sId + '_' + forMember;
            }
        }

        window.topWindow.$util.inform('Playing ' + sId + ' sound'); // Useful when debugging sounds when testing using SU, otherwise you don't know which window they came from

        window.topWindow.soundManager.play(sId);
    }

    function chatLoad(roomId) {
        window.topWindow = window;

        try {
            document.getElementById('post').focus();
        } catch (ignore) {}

        if ($cms.pageUrl().searchParams.get('keep_chattest') !== '1') {
            beginChatting(roomId);
        }

        window.textColour = document.getElementById('text_colour');
        if (window.textColour) {
            window.textColour.style.color = window.textColour.value;
        }

        $cms.ui.manageScrollHeight(document.getElementById('post'));
    }

    function beginChatting(roomId) {
        window.loadFromRoomId = roomId;

        chatCheck(true, 0);
        playChatSound('you_connect');
    }

    function getTickedPeople(form) {
        var people = '';

        for (var i = 0; i < form.elements.length; i++) {
            if ((form.elements[i].type === 'checkbox') && (form.elements[i].checked)) {
                people += ((people !== '') ? ',' : '') + form.elements[i].name.substr(7);
            }
        }

        if (people === '') {
            $cms.ui.alert('{!chat:NOONE_SELECTED_YET;^}');
            return '';
        }

        return people;
    }

    window.doInputPrivateMessage = function doInputPrivateMessage(fieldName) {
        $cms.ui.prompt('{!chat:ENTER_RECIPIENT;^}', '', null, '{!chat:INPUT_CHATCODE_private_message;^}').then(function (va) {
            if (va != null) {
                $cms.ui.prompt(
                    '{!MESSAGE;^}',
                    '',
                    function (vb) {
                        if (vb != null) {
                            window.$editing.insertTextbox(document.getElementById(fieldName), '[private="' + va + '"]' + vb + '[/private]');
                        }
                    },
                    '{!chat:INPUT_CHATCODE_private_message;^}'
                );
            }
        });
    };

    window.doInputInvite = function doInputInvite(fieldName) {
        $cms.ui.prompt(
            '{!chat:ENTER_RECIPIENT;^}',
            '',
            function (va) {
                if (va != null) {
                    $cms.ui.prompt(
                        '{!chat:ENTER_CHATROOM;^}',
                        '',
                        function (vb) {
                            if (vb != null) {
                                window.$editing.insertTextbox(document.getElementById(fieldName), '[invite="' + va + '"]' + vb + '[/invite]');
                            }
                        },
                        '{!chat:INPUT_CHATCODE_invite;^}'
                    );
                }
            },
            '{!chat:INPUT_CHATCODE_invite;^}'
        );
    };

    window.doInputNewRoom = function doInputNewRoom(fieldName) {
        $cms.ui.prompt('{!chat:ENTER_CHATROOM;^}', '', null, '{!chat:INPUT_CHATCODE_new_room;^}').then(function (chatroomName) {
            if (chatroomName != null) {
                $cms.ui.prompt('{!chat:ENTER_ALLOW;^}', '', null, '{!chat:INPUT_CHATCODE_new_room;^}').then(function (allowList) {
                    if (allowList != null) {
                        window.$editing.insertTextbox(document.getElementById(fieldName), '[newroom="' + chatroomName + '"]' + allowList + '[/newroom]');
                    }
                });
            }
        });
    };

    // Post a chat message
    function chatPost(event, currentRoomId, fieldName, fontName, fontColour) {
        // Catch the data being submitted by the form, and send it through XMLHttpRequest if possible. Stop the form submission if this is achieved.
        var element = document.getElementById(fieldName);
        var messageText = strVal(element.value);

        if (messageText !== '') {
            window.topWindow.$util.inform('Posting chat message (' + new Date().getTime() + ')');

            if (window.topWindow.ccTimer) {
                window.topWindow.$util.inform('Clearing existing chat timer as this posting will take control until finished (' + new Date().getTime() + ')');

                window.topWindow.clearTimeout(window.topWindow.ccTimer);
                window.topWindow.ccTimer = null;
            }

            // Reinvite last left member if necessary
            if ((element.forceInvite !== undefined) && (element.forceInvite !== null)) {
                inviteIm(element.forceInvite);
                element.forceInvite = null;
            }

            // Send it through XMLHttpRequest, and append the results.
            var url = '{$FIND_SCRIPT_NOHTTP;,messages}?action=post';
            element.disabled = true;
            window.topWindow.currentlySendingMessage = true;
            var fullUrl = $util.rel($cms.maintainThemeInLink(url + window.topWindow.$cms.keep()));
            var postData = 'room_id=' + encodeURIComponent(currentRoomId) + '&message=' + encodeURIComponent(messageText) + '&font=' + encodeURIComponent(fontName) + '&colour=' + encodeURIComponent(fontColour) + '&message_id=' + encodeURIComponent((window.topWindow.lastMessageId === null) ? -1 : window.topWindow.lastMessageId) + '&event_id=' + encodeURIComponent(window.topWindow.lastEventId);
            $cms.doAjaxRequest(fullUrl, function (responseXML, xhr) {
                if (responseXML != null) {
                    window.topWindow.currentlySendingMessage = false;
                    element.disabled = false;
                    var responses = responseXML.getElementsByTagName('result');
                    if (responses[0]) {
                        processChatXmlMessages(responses[0], true);

                        setTimeout(function () {
                            element.value = '';
                        }, 20);
                        element.style.height = 'auto';

                        playChatSound('message_sent');
                    } else {
                        $cms.ui.alert('{!chat:MESSAGE_POSTING_ERROR;^}');
                    }

                    try {
                        element.focus();
                    } catch (e) {}
                } else {
                    if (xhr.status === 200) {
                        window.topWindow.$util.inform('Successfully posted chat message (' + new Date().getTime() + ')');
                    } else {
                        $cms.ui.alert('{!chat:MESSAGE_POSTING_ERROR;^}');
                    }

                    window.topWindow.currentlySendingMessage = false;
                    element.disabled = false;
                }

                // Reschedule the next check (ccTimer was reset already higher up in function)
                window.topWindow.$util.inform('Setting new chat timer (' + new Date().getTime() + ')');
                window.topWindow.ccTimer = window.topWindow.setTimeout(function () {
                    window.topWindow.chatCheck(false, window.topWindow.lastMessageId, window.topWindow.lastEventId);
                }, window.MESSAGE_CHECK_INTERVAL);
            }, postData);
        }

        event.preventDefault();
    }

    // Check for new messages
    function chatCheck(backlog, messageId, eventId) {
        if (window.currentlySendingMessage) { // We'll reschedule once our currently-in-progress message is sent
            window.topWindow.$util.inform('Skip checking for chat messages (chat timer), as a message posting is pending completion (' + new Date().getTime() + ')');
            return;
        }

        window.topWindow.$util.inform('Checking for chat messages (chat timer) (' + new Date().getTime() + ')');

        eventId = intVal(eventId, -1); // -1 Means, we don't want to look at events, but the server will give us a null event

        // Check for new messages on the server the new or old way
        setTimeout(function () {
            chatCheckTimeout(backlog, messageId, eventId);
        }, window.MESSAGE_CHECK_INTERVAL * 1.2);

        var theDate = new Date();
        if (!window.messageChecking || (window.messageChecking + window.MESSAGE_CHECK_INTERVAL <= theDate.getTime())) { // If not currently in process, or process stalled
            window.messageChecking = theDate.getTime();
            var url;
            var _roomId = (window.loadFromRoomId == null) ? -1 : window.loadFromRoomId;
            if (backlog) {
                url = '{$FIND_SCRIPT_NOHTTP;,messages}?action=all&room_id=' + encodeURIComponent(_roomId);
            } else {
                url = '{$FIND_SCRIPT_NOHTTP;,messages}?action=new&room_id=' + encodeURIComponent(_roomId) + '&message_id=' + encodeURIComponent(messageId ? messageId : '-1') + '&event_id=' + encodeURIComponent(eventId);
            }
            if ($cms.pageUrl().searchParams.get('no_reenter_message') === '1') {
                url = url + '&no_reenter_message=1';
            }
            var fullUrl = $util.rel($cms.maintainThemeInLink(url + $cms.keep()));
            $cms.doAjaxRequest(fullUrl, function (responseXML, xhr) {
                if (responseXML != null) {
                    chatCheckResponse(responseXML, xhr, /*skipIncomingSound*/backlog);
                } else {
                    if ((xhr.status !== 200) && (backlog)) {
                        $cms.ui.alert('{!chat:MESSAGE_CHECKING_ERROR;^}');
                    }

                    chatCheckResponse(null, null);
                }
            });
        } else {
            window.topWindow.$util.inform('Skip checking for chat messages (chat timer), as a previous check is pending completion and not yet timed out (' + new Date().getTime() + ')');
        }
    }
    window.chatCheck = chatCheck;

    // Check to see if there's been a packet loss
    function chatCheckTimeout(backlog, messageId, eventId) {
        var theDate = new Date();
        if ((window.messageChecking) && (window.messageChecking <= theDate.getTime() - window.MESSAGE_CHECK_INTERVAL * 1.2) && (!window.currentlySendingMessage)) { // If we are awaiting a response (messageChecking is not false, and that response was made more than 12 seconds ago
            window.topWindow.$util.inform('(Guard) Making sure our last actioned chat check completed and was on time - and it did not! (' + new Date().getTime() + ')');

            // Our response is tardy - presume we've lost our scheduler / AJAX request, so fire off a new AJAX request and reset the chatCheckTimeout timer
            chatCheck(backlog, messageId, eventId);
        } else {
            window.topWindow.$util.inform('(Guard) Making sure our last actioned chat check completed and was on time - and it did (' + new Date().getTime() + ')');
        }
    }

    // Deal with the new messages response. Wraps around processChatXmlMessages as it also adds timers to ensure the message check continues to function even if background errors might have happened.
    function chatCheckResponse(responseXML, xhr, skipIncomingSound) {
        var ajaxResult = responseXML && responseXML.querySelector('result');

        if (ajaxResult != null) {
            window.topWindow.$util.inform('Received chat check response (' + new Date().getTime() + ')');

            if (skipIncomingSound === undefined) {
                skipIncomingSound = false;
            }

            var temp = processChatXmlMessages(ajaxResult, skipIncomingSound);
            if (Number(temp) === -2) {
                return false;
            }
        } else {
            window.topWindow.$util.inform('Chat check failed (' + new Date().getTime() + ')');
        }

        // Schedule the next check
        window.topWindow.$util.inform('Schedule next chat message check (chat timer) (' + new Date().getTime() + ')');
        if (window.ccTimer) {
            clearTimeout(window.ccTimer);
            window.ccTimer = null;
        }
        window.ccTimer = setTimeout(function () {
            chatCheck(false, window.lastMessageId, window.lastEventId);
        }, window.MESSAGE_CHECK_INTERVAL);

        window.messageChecking = false; // All must be ok so say we are happy we got a response and scheduled the next check
        return true;
    }

    function processChatXmlMessages(ajaxResult, skipIncomingSound) {
        if (!ajaxResult) { // Some kind of error happened
            return;
        }

        skipIncomingSound = Boolean(skipIncomingSound);

        var messages = arrVal(ajaxResult.children),
            messageContainer = document.getElementById('messages-window'),
            messageContainerGlobal = (messageContainer != null),
            clonedMessage,
            currentRoomId = window.loadFromRoomId,
            tabElement,
            flashableAlert = false,
            username, roomName, roomId, eventType, memberId, tmpElement, rooms, avatarUrl, participants,
            id, timestamp,
            firstSet = false,
            newestIdHere = null, newestTimestampHere = null;

        // Look through our messages
        messages.forEach(function (xmlEl) {
            if (xmlEl.localName === 'div') { // MESSAGE
                // Find out about our message
                id = xmlEl.getAttribute('id');
                timestamp = xmlEl.getAttribute('timestamp');
                if (((window.topWindow.lastMessageId) && (parseInt(id) <= window.topWindow.lastMessageId)) && ((window.topWindow.lastTimestamp) && (parseInt(timestamp) <= window.topWindow.lastTimestamp))) {
                    return; // continue
                }

                // Find container to place message
                if (!messageContainerGlobal) {
                    roomId = xmlEl.getAttribute('room_id');
                    currentRoomId = roomId;
                    messageContainer = null;
                } else {
                    currentRoomId = xmlEl.getAttribute('room_id');
                }

                if (document.getElementById('messages-window-' + currentRoomId)) {
                    messageContainer = document.getElementById('messages-window-' + currentRoomId);
                    tabElement = document.getElementById('tab-' + currentRoomId);
                    if ((tabElement) && (!tabElement.classList.contains('chat-lobby-convos-current-tab'))) {
                        tabElement.className = ((tabElement.classList.contains('chat-lobby-convos-tab-first')) ? 'chat-lobby-convos-tab-first ' : '') + 'chat-lobby-convos-tab-new-messages';
                    }
                } else if ((window.openedPopups['room_' + currentRoomId] !== undefined) && (!window.openedPopups['room_' + currentRoomId].isShutdown) && (window.openedPopups['room_' + currentRoomId].document)) { // Popup
                    messageContainer = window.openedPopups['room_' + currentRoomId].document.getElementById('messages-window-' + currentRoomId);
                }

                if (!messageContainer) {
                    return; // Still no luck
                }

                // If we got this far, recognise the message as received
                newestIdHere = parseInt(id);
                if ((newestTimestampHere = null) || (newestTimestampHere < parseInt(timestamp))) {
                    newestTimestampHere = parseInt(timestamp);
                }

                var doc = document;
                if (window.openedPopups['room_' + currentRoomId] !== undefined) {
                    var popupWin = window.openedPopups['room_' + currentRoomId];
                    if (!popupWin.document) { // We have nowhere to put the message
                        return; // continue
                    }
                    doc = popupWin.document;

                    // Feed in details, so if it becomes autonomous, it knows what to run with
                    popupWin.lastTimestamp = window.lastTimestamp;
                    popupWin.lastEventId = window.lastEventId;
                    if ((newestIdHere) && ((popupWin.lastMessageId == null) || (popupWin.lastMessageId < newestIdHere))) {
                        popupWin.lastMessageId = newestIdHere;
                    }
                    if (popupWin.lastTimestamp < newestTimestampHere) {
                        popupWin.lastTimestamp = newestTimestampHere;
                    }
                }

                if (doc.getElementById('chat_message__' + id)) { // Already there
                    return; // continue
                }

                // Clone the node so that we may insert it
                clonedMessage = doc.createElement('div');
                $dom.html(clonedMessage, (xmlEl.xml !== undefined) ? xmlEl.xml/*IE-only optimisation*/ : xmlEl.firstElementChild.outerHTML);
                clonedMessage = clonedMessage.firstElementChild;
                clonedMessage.id = 'chat_message__' + id;

                // Non-first message
                if (messageContainer.children.length > 0) {
                    if ($cms.configOption('chat_message_direction') === 'upwards') {
                        messageContainer.insertBefore(clonedMessage, messageContainer.firstElementChild);
                    } else if ($cms.configOption('chat_message_direction') === 'downwards') {
                        messageContainer.appendChild(clonedMessage);
                        messageContainer.scrollTop = 1000000;
                    }

                    if (!firstSet) {// Only if no other message sound already for this event update
                        if (!skipIncomingSound) {
                            playChatSound(document.hidden ? 'message_background' : 'message_received', xmlEl.getAttribute('sender_id'));
                        }
                        flashableAlert = true;
                    }
                } else { // First message
                    $dom.html(messageContainer, '');
                    messageContainer.appendChild(clonedMessage);
                    firstSet = true; // Let the code know the first set of messages has started, squashing any extra sounds for this event update
                    if (!skipIncomingSound) {
                        playChatSound('message_initial');
                    }
                }

                if (!messageContainerGlobal) {
                    currentRoomId = -1; // We'll be gathering for all rooms we're in now, because this messaging is coming through the top-level window
                }
            } else if (xmlEl.nodeName.toLowerCase() === 'chat_members_update') { /* eslint-disable-line no-restricted-properties */ // UPDATE MEMBERS LIST IN ROOM
                var membersElement = document.getElementById('chat-members-update');
                if (membersElement) {
                    $dom.html(membersElement, xmlEl.textContent);
                }
            } else if ((xmlEl.nodeName.toLowerCase() === 'chat_event') && (window.imParticipantTemplate !== undefined)) { /* eslint-disable-line no-restricted-properties */ // Some kind of transitory event
                eventType = xmlEl.getAttribute('event_type');
                roomId = xmlEl.getAttribute('room_id');
                memberId = xmlEl.getAttribute('member_id');
                username = xmlEl.getAttribute('username');
                avatarUrl = xmlEl.getAttribute('avatar_url');

                id = xmlEl.textContent;

                switch (eventType) {
                    case 'BECOME_ACTIVE':
                        if (window.TRANSITORY_ALERT_TIME !== 0) {
                            flashableAlert = true;
                            tmpElement = document.getElementById('online-' + memberId);
                            if (tmpElement) {
                                if ($dom.html(tmpElement).toLowerCase() === '{!chat:ACTIVE;^}'.toLowerCase()) {
                                    break;
                                }
                                $dom.html(tmpElement, '{!chat:ACTIVE;^}');
                                var friendImg = document.getElementById('friend-img-' + memberId);
                                if (friendImg) {
                                    friendImg.className = 'friend-active';
                                }
                                var alertBoxWrap = document.getElementById('alert-box-wrap');
                                if (alertBoxWrap) {
                                    alertBoxWrap.style.display = 'block';
                                }
                                var alertBox = document.getElementById('alert-box');
                                if (alertBox) {
                                    $dom.html(alertBox, '{!chat:NOW_ONLINE;^}'.replace('{' + '1}', username));
                                }
                                setTimeout(function () {
                                    if (document.getElementById('alert-box')) { // If the alert box is still there, remove it
                                        alertBoxWrap.style.display = 'none';
                                    }
                                }, window.TRANSITORY_ALERT_TIME);

                                if (!skipIncomingSound) {
                                    playChatSound('contact_on', memberId);
                                }
                            } else if (!document.getElementById('chat-lobby-convos-tabs')) {
                                createOverlayEvent(/*skip_incoming_sound*/true, memberId, '{!chat:NOW_ONLINE;^}'.replace('{' + '1}', username), function () {
                                    startIm(memberId, true);
                                    return false;
                                }, avatarUrl);
                            }
                        }

                        rooms = findImConvoRoomIds();
                        for (var r in rooms) {
                            roomId = rooms[r];
                            var doc2 = document;
                            if ((window.openedPopups['room_' + roomId] !== undefined) && (!window.openedPopups['room_' + roomId].isShutdown)) {
                                if (!window.openedPopups['room_' + roomId].document) {
                                    continue;
                                }
                                doc2 = window.openedPopups['room_' + roomId].document;
                            }
                            tmpElement = doc2.getElementById('participant-online--' + roomId + '--' + memberId);
                            if (tmpElement) {
                                $dom.html(tmpElement, '{!chat:ACTIVE;^}');
                            }
                        }
                        break;

                    case 'BECOME_INACTIVE':
                        var friendBeingTracked = false;
                        tmpElement = document.getElementById('online-' + memberId);
                        if (tmpElement) {
                            if ($dom.html(tmpElement).toLowerCase() === '{!chat:INACTIVE;^}'.toLowerCase()) {
                                break;
                            }
                            $dom.html(tmpElement, '{!chat:INACTIVE;^}');
                            document.getElementById('friend-img-' + memberId).classList.remove('friend-active');
                            document.getElementById('friend-img-' + memberId).classList.add('friend-inactive');
                            friendBeingTracked = true;
                        }

                        rooms = findImConvoRoomIds();
                        for (var k in rooms) {
                            roomId = rooms[k];
                            var doc3 = document;
                            if (window.openedPopups['room_' + roomId] !== undefined) {
                                if (!window.openedPopups['room_' + roomId].document) {
                                    continue;
                                }
                                doc3 = window.openedPopups['room_' + roomId].document;
                            }
                            tmpElement = doc3.getElementById('participant-online--' + roomId + '--' + memberId);
                            if (tmpElement) {
                                $dom.html(tmpElement, '{!chat:INACTIVE;^}');
                            }
                            friendBeingTracked = true;
                        }

                        if (!skipIncomingSound) {
                            if (friendBeingTracked) {
                                playChatSound('contact_off', memberId);
                            }
                        }
                        break;

                    case 'JOIN_IM':
                        addImMember(roomId, memberId, username, xmlEl.getAttribute('away') === '1', avatarUrl);

                        var doc4 = document;
                        if ((window.openedPopups['room_' + roomId] !== undefined) && (!window.openedPopups['room_' + roomId].isShutdown)) {
                            if (!window.openedPopups['room_' + roomId].document) {
                                break;
                            }
                            doc4 = window.openedPopups['room_' + roomId].document;
                        }
                        tmpElement = doc4.getElementById('participant-online--' + roomId + '--' + memberId);
                        if (tmpElement) {
                            if ($dom.html(tmpElement).toLowerCase() === '{!chat:ACTIVE;^}'.toLowerCase()) {
                                break;
                            }
                            $dom.html(tmpElement, '{!chat:ACTIVE;^}');
                            document.getElementById('friend-img-' + memberId).className = 'friend-active';
                        }

                        if (!skipIncomingSound) {
                            playChatSound('contact_on', memberId);
                        }
                        break;

                    case 'PREINVITED_TO_IM':
                        addImMember(roomId, memberId, username, xmlEl.getAttribute('away') === '1', avatarUrl);
                        break;

                    case 'DEINVOLVE_IM':
                        var doc5 = document;
                        if (window.openedPopups['room_' + roomId] !== undefined) {
                            if (!window.openedPopups['room_' + roomId].document) {
                                break;
                            }
                            doc5 = window.openedPopups['room_' + roomId].document;
                        }

                        tmpElement = doc5.getElementById('participant__' + roomId + '__' + memberId);
                        if ((tmpElement) && (tmpElement.parentNode)) {
                            var parent = tmpElement.parentNode;
                            /*Actually prefer to let them go away it's cleaner if (parent.childNodes.length == 1) // Don't really let them go, flag them merely as away - we'll reinvite them upon next post
                             {
                             tmp_element = doc.getElementById('post_' + roomId);
                             if (tmp_element) tmp_element.forceInvite = member_id;

                             tmp_element=doc.getElementById('participant-online--' + roomId + '--' + member_id);
                             if (tmp_element)
                             {
                             if ($dom.html(tmp_element).toLowerCase() == '{!chat:INACTIVE;^}'.toLowerCase()) break;
                             $dom.html(tmp_element, '{!chat:INACTIVE;^}');
                             }
                             } else*/
                            //{
                            parent.removeChild(tmpElement);
                            //}
                            /*if (parent.childNodes.length==0) { Don't set to none, as we want to allow the 'forceInvite' IM re-activation feature, to draw the other guy back -- above we pretended they're merely 'away', not just left
                             $dom.html(parent, '<em class="none">{!NONE;^}</em>');
                             }*/

                            if (!skipIncomingSound) {
                                playChatSound('contact_off', memberId);
                            }
                        }
                        break;
                }
            } else if ((xmlEl.nodeName.toLowerCase() === 'chat_invite') && (window.imParticipantTemplate !== undefined)) { /* eslint-disable-line no-restricted-properties */ // INVITES
                roomId = xmlEl.textContent;

                if ((!document.getElementById('room-' + roomId)) && ((window.openedPopups['room_' + roomId] === undefined) || (window.openedPopups['room_' + roomId].isShutdown))) {
                    roomName = xmlEl.getAttribute('room_name');
                    avatarUrl = xmlEl.getAttribute('avatar_url');
                    participants = xmlEl.getAttribute('participants');
                    var byYou = (xmlEl.getAttribute('inviter') === xmlEl.getAttribute('you'));

                    if ((!byYou) && (!window.instantGo) && (!document.getElementById('chat-lobby-convos-tabs'))) {
                        createOverlayEvent(skipIncomingSound, xmlEl.getAttribute('inviter'), '{!chat:IM_INFO_CHAT_WITH;^}'.replace('{' + '1}', roomName), function () {
                            window.lastMessageId = -1; // Ensure messages re-processed
                            detectedConversation(roomId, roomName, participants);
                            return false;
                        }, avatarUrl, roomId);
                    } else {
                        detectedConversation(roomId, roomName, participants);
                    }
                    flashableAlert = true;
                }

            } else if (xmlEl.nodeName.toLowerCase() === 'chat_tracking') { /* eslint-disable-line no-restricted-properties */ // TRACKING
                window.topWindow.lastMessageId = xmlEl.getAttribute('last_msg');
                window.topWindow.lastEventId = xmlEl.getAttribute('last_event');
            }
        });

        // Get attention, to indicate something has happened
        if (flashableAlert) {
            if ((roomId) && (window.openedPopups['room_' + roomId] !== undefined) && (!window.openedPopups['room_' + roomId].isShutdown)) {
                if (window.openedPopups['room_' + roomId].focus !== undefined) {
                    try {
                        window.openedPopups['room_' + roomId].focus();
                    }
                    catch (e) {
                    }
                }
                if (window.openedPopups['room_' + roomId].document) {
                    var post = window.openedPopups['room_' + roomId].document.getElementById('post');
                    if (post) {
                        try {
                            post.focus();
                        }
                        catch (e) {
                        }
                    }
                }
            } else {
                if (window.focus !== undefined) {
                    try {
                        window.focus();
                    } catch (e) {}
                }
                var post2 = document.getElementById('post');
                if (post2 && post2.name === 'message'/*The chat posting field is named message and IDd post*/) {
                    try {
                        post2.focus();
                    } catch (e) {}
                }
            }
        }

        if (window.topWindow.lastTimestamp < newestTimestampHere) {
            window.topWindow.lastTimestamp = newestTimestampHere;
        }

        return currentRoomId;

        function addImMember(roomId, memberId, username, away, avatarUrl) {
            setTimeout(function () {
                var doc = document;
                if (window.openedPopups['room_' + roomId] !== undefined) {
                    if (window.openedPopups['room_' + roomId].isShutdown) {
                        return;
                    }
                    if (!window.openedPopups['room_' + roomId].document) {
                        return;
                    }
                    doc = window.openedPopups['room_' + roomId].document;
                }
                if (away) {
                    var tmpElement = doc.getElementById('online-' + memberId);
                    if ((tmpElement) && ($dom.html(tmpElement).toLowerCase() === '{!chat:ACTIVE;^}'.toLowerCase())) {
                        away = false;
                    }
                }
                if (doc.getElementById('participant__' + roomId + '__' + memberId)) { // They're already put in it
                    return;
                }
                var newParticipant = doc.createElement('div');
                var newParticipantInner = window.imParticipantTemplate.replace(/__username__/g, username);
                newParticipantInner = newParticipantInner.replace(/__id__/g, memberId);
                newParticipantInner = newParticipantInner.replace(/__room_id__/g, roomId);
                newParticipantInner = newParticipantInner.replace(/__avatar_url__/g, avatarUrl);
                if (avatarUrl === '') {
                    newParticipantInner = newParticipantInner.replace('style="display: block" id="avatar--', 'style="display: none" id="avatar--');
                }
                newParticipantInner = newParticipantInner.replace(/__online__/g, away ? '{!chat:INACTIVE;^}' : '{!chat:ACTIVE;^}');
                $dom.html(newParticipant, newParticipantInner);
                newParticipant.id = 'participant__' + roomId + '__' + memberId;
                var element = doc.getElementById('participants--' + roomId);
                if (element) {// If we've actually got the HTML for the room setup
                    var pList = $dom.html(element).toLowerCase();

                    if (pList.includes('<em class="none">') || pList.includes('<em class="loading">')) {
                        $dom.html(element, '');
                    }
                    element.appendChild(newParticipant);
                    if (doc.getElementById('friend-img-' + memberId)) {
                        doc.getElementById('friend--' + memberId).style.display = 'none';
                    }
                }
            }, 0);
        }

        function detectedConversation(roomId, roomName, participants) { // Assumes conversation is new: something must check that before calling here
            window.topWindow.lastEventId = -1; // So that invite events re-run

            var areas = document.getElementById('chat-lobby-convos-areas');
            var tabs = document.getElementById('chat-lobby-convos-tabs');
            var lobby, count;
            if (tabs) {// Chat lobby
                tabs.style.display = 'block';
                if (document.getElementById('invite-ongoing-im-button')) {
                    document.getElementById('invite-ongoing-im-button').disabled = false;
                }
                count = countImConvos();
                // First one?
                if (count === 0) {
                    window.noImHtml = $dom.html(areas);
                    $dom.html(areas, '');
                    $dom.html(tabs, '');
                }

                lobby = true;
            } else {// Not chat lobby (sitewide IM)
                lobby = false;
            }

            window.topWindow.allConversations[participants] = roomId;

            var url = '{$FIND_SCRIPT_NOHTTP;,messages}?action=join_im&event_id=' + window.topWindow.lastEventId + window.topWindow.$cms.keep();
            var post = 'room_id=' + encodeURIComponent(roomId);

            // Add in
            var newOne = window.imAreaTemplate.replace(/__room_id__/g, roomId).replace(/__room_name__/g, roomName);
            if (lobby) {
                var newDiv;
                newDiv = document.createElement('div');
                $dom.html(newDiv, newOne);
                areas.appendChild(newDiv);

                // Add tab
                newDiv = document.createElement('div');
                newDiv.className = 'chat-lobby-convos-tab-uptodate' + ((count === 0) ? ' chat-lobby-convos-tab-first' : '');
                $dom.html(newDiv, $cms.filter.html(roomName));
                newDiv.id = 'tab-' + roomId;
                newDiv.participants = participants;
                $dom.on(newDiv, 'click', function () {
                    chatSelectTab(newDiv);
                });
                tabs.appendChild(newDiv);
                chatSelectTab(newDiv);

                // Tell server we've joined
                $cms.doAjaxRequest(url, [function (responseXml) {
                    var ajaxResult = responseXml && responseXml.querySelector('result');
                    processChatXmlMessages(ajaxResult, true);
                }], post);
            } else {
                // Open pop-up
                var imPopupWindowOptions = 'width=370,height=460,menubar=no,toolbar=no,location=no,resizable=no,scrollbars=yes,top=' + ((screen.height - 520) / 2) + ',left=' + ((screen.width - 440) / 2);
                var newWindow = window.open($util.rel('data/empty.html?instant_messaging'), 'room_' + roomId, imPopupWindowOptions); // The "?instant_messaging" is just to make the location bar less surprising to the user ;-) [modern browsers always show the location bar for security, even if we try and disable it]
                if (!newWindow || (newWindow.window === undefined /*BetterPopupBlocker for Chrome returns a fake new window but won't have this defined in it*/)) {
                    $cms.ui.alert('{!chat:_FAILED_TO_OPEN_POPUP;,{$PAGE_LINK*,_SEARCH:popup_blockers:failure=1,0,1}}', '{!chat:FAILED_TO_OPEN_POPUP;^}', true);
                }
                setTimeout(function () { // Needed for Safari to set the right domain, and also to give window an opportunity to attach itself on its own accord
                    if ((window.openedPopups['room_' + roomId] != null) && (!window.openedPopups['room_' + roomId].isShutdown)) { // It's been reattached already
                        return;
                    }

                    window.openedPopups['room_' + roomId] = newWindow;

                    if (newWindow && (newWindow.document !== undefined)) {
                        newWindow.document.open();
                        newWindow.document.write(newOne); // This causes a blocking on Firefox while files download/parse. It's annoying, you'll see the pop-up freezes. But it works after a few seconds.
                        newWindow.document.close();
                        newWindow.topWindow = window;
                        newWindow.roomId = roomId;
                        newWindow.loadFromRoomId = -1;

                        setTimeout(function () { // Allow XHTML to render; needed for .document to be available, which is needed to write in seeded chat messages
                            if (!newWindow.document) {
                                return;
                            }

                            newWindow.participants = participants;

                            newWindow.onbeforeunload = function () {
                                return '{!chat:CLOSE_VIA_END_CHAT_BUTTON;^}';
                                //new_window.closeChatConversation(roomId);
                            };

                            try {
                                newWindow.focus();
                            } catch (e) {}

                            // Tell server we have joined
                            $cms.doAjaxRequest(url, [function (responseXml) {
                                var ajaxResult = responseXml && responseXml.querySelector('result');
                                processChatXmlMessages(ajaxResult, true);
                            }], post);

                            // Set title
                            var domTitle = newWindow.document.querySelector('title');
                            if (domTitle != null) {
                                newWindow.document.title = $dom.html(domTitle).replace(/<.*?>/g, '');
                            } // For Safari

                        }, 500);
                        /* Could be 60 except for Firefox which is slow */
                    }
                }, 60);
            }
        }
    }

    function createOverlayEvent(skipIncomingSound, memberId, message, clickEvent, avatarUrl, roomId) {
        var div;

        // Close link
        function closePopup() {
            if (div) {
                if (roomId) {
                    $cms.ui.generateQuestionUi(
                        '{!chat:HOW_REMOVE_CHAT_NOTIFICATION;^}',
                        {/*'buttons/cancel': '{!INPUTSYSTEM_CANCEL;^}',*/'buttons/proceed': '{!CLOSE;^}', 'buttons/ignore': '{!HIDE;^}'},
                        '{!chat:REMOVE_CHAT_NOTIFICATION;^}',
                        null,
                        function (answer) {
                            /*if (answer.toLowerCase()=='{!INPUTSYSTEM_CANCEL;^}'.toLowerCase()) return;*/
                            if (answer.toLowerCase() === '{!CLOSE;^}'.toLowerCase()) {
                                deinvolveIm(roomId, false, false);
                            }
                            document.body.removeChild(div);
                            div = null;
                        }
                    );
                } else {
                    document.body.removeChild(div);
                    div = null;
                }
            }
        }

        if (roomId === undefined) {
            roomId = null;
        }

        if (window !== window.topWindow) { // Can't display in an autonomous pop-up
            return;
        }

        // Make sure to not show multiple equiv ones, which could happen in various situations
        if (roomId !== null) {
            if ((window.alreadyReceivedRoomInvites[roomId] !== undefined) && (window.alreadyReceivedRoomInvites[roomId])) {
                return;
            }
            window.alreadyReceivedRoomInvites[roomId] = true;
        } else {
            if ((window.alreadyReceivedContactAlert[memberId] !== undefined) && (window.alreadyReceivedContactAlert[memberId])) {
                return;
            }
            window.alreadyReceivedContactAlert[memberId] = true;
        }

        // Ping!
        if (!skipIncomingSound) {
            playChatSound('invited', memberId);
        }

        // Start DOM stuff
        div = document.createElement('div');
        div.className = 'im-event';
        //div.style.left=($dom.getWindowWidth()/2-140)+'px';
        div.style.right = '1em';
        div.style.bottom = ((document.body.querySelectorAll('.im-event').length) * 185 + 20) + 'px';
        /*{+START,SET,icon_buttons_proceed2}{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END}{+END}*/
        var links = document.createElement('ul');
        links.className = 'actions-list';

        var imgClose = document.createElement('img');
        imgClose.src = $util.srl('{$IMG;,icons/admin/delete}');
        imgClose.width = '14';
        imgClose.height = '14';
        imgClose.className = 'im-popup-close-button blend';
        $dom.on(imgClose, 'click', closePopup);
        div.appendChild(imgClose);

        // Avatar
        if (avatarUrl) {
            var img1 = document.createElement('img');
            img1.src = avatarUrl;
            img1.className = 'im-popup-avatar';
            div.appendChild(img1);
        }

        // Message
        var pMessage = document.createElement('p');
        $dom.html(pMessage, message);
        div.appendChild(pMessage);

        // Open link
        if (!$cms.browserMatches('non_concurrent')) { // Can't do on iOS due to not being able to run windows/tabs concurrently - so for iOS we only show a lobby link
            var aPopupOpen = document.createElement('a');
            aPopupOpen.href = '#!';
            $dom.on(aPopupOpen, 'click', function (e) {
                clickEvent();
                document.body.removeChild(div);
                div = null;
                e.preventDefault();
            });
            $dom.html(aPopupOpen, '{!chat:OPEN_IM_POPUP;^}');
            var liPopupOpen = document.createElement('li');
            $dom.html(liPopupOpen, '{$GET;^,icon_buttons_proceed2} ');
            liPopupOpen.appendChild(aPopupOpen);
            links.appendChild(liPopupOpen);
        }

        // Lobby link
        var aGotoLobby = document.createElement('a');
        aGotoLobby.href = window.lobbyLink.replace('%21%21', memberId);
        aGotoLobby.target = '_blank';
        $dom.on(aGotoLobby, 'click', closePopup);
        $dom.html(aGotoLobby, '{!chat:GOTO_CHAT_LOBBY;^}');
        var liGotoLobby = document.createElement('li');
        $dom.html(liGotoLobby, '{$GET;^,icon_buttons_proceed2} ');
        liGotoLobby.appendChild(aGotoLobby);
        links.appendChild(liGotoLobby);

        // Add it all in
        div.appendChild(links);
        document.body.appendChild(div);

        // Contact ones disappear after a time
        if (roomId === null) {
            setTimeout(function () {
                closePopup();
            }, window.TRANSITORY_ALERT_TIME);
        }
    }

    function startIm(people, justRefocus) {
        if (($cms.browserMatches('non_concurrent')) && !document.getElementById('chat-lobby-convos-tabs')) {
            // Let it navigate to chat lobby
            return true;
        }

        people = strVal(people);
        justRefocus = Boolean(justRefocus);

        var message = people.includes(',') ? '{!chat:ALREADY_HAVE_THIS;^}' : '{!chat:ALREADY_HAVE_THIS_SINGLE;^}';

        if (window.topWindow.allConversations[people] != null) {
            if (justRefocus) {
                try {
                    var roomId = window.topWindow.allConversations[people];
                    if (document.getElementById('tab-' + roomId)) {
                        chatSelectTab(document.getElementById('tab-' + roomId));
                    } else {
                        window.topWindow.openedPopups['room_' + roomId].focus();
                    }
                    return false;
                } catch (ignore) {}
            }

            $cms.ui.confirm(
                message,
                function (answer) {
                    if (answer) { // false, because can't recycle if its already open
                        _startIm(people, false);
                    }
                }
            );
        } else {
            _startIm(people, true); // true, because an IM may exist we don't have open, so let that be recycled
        }

        function _startIm(people, mayRecycle) {
            var div = document.createElement('div');
            div.className = 'loading-overlay';
            $dom.html(div, '{!LOADING;^}');
            document.body.appendChild(div);
            $cms.doAjaxRequest($util.rel($cms.maintainThemeInLink('{$FIND_SCRIPT_NOHTTP;,messages}?action=start_im&message_id=' + encodeURIComponent((window.topWindow.lastMessageId === null) ? -1 : window.topWindow.lastMessageId) + '&mayRecycle=' + (mayRecycle ? '1' : '0') + '&event_id=' + encodeURIComponent(window.topWindow.lastEventId) + $cms.keep())), [function (responseXml) {
                var result = responseXml.querySelector('result');
                if (result) {
                    window.instantGo = true;
                    processChatXmlMessages(result, true);
                    window.instantGo = false;
                }
                document.body.removeChild(div);
            }], 'people=' + people);
        }

        return false;
    }

    function inviteIm(people) {
        var roomId = findCurrentImRoom();
        if (!roomId) {
            $cms.ui.alert('{!chat:NO_IM_ACTIVE;^}');
        } else {
            $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,messages}?action=invite_im' + $cms.keep(), null, 'room_id=' + encodeURIComponent(roomId) + '&people=' + people);
        }
    }

    function countImConvos() {
        var chatLobbyConvosTabs = document.getElementById('chat-lobby-convos-tabs'),
            count = 0, i;

        for (i = 0; i < chatLobbyConvosTabs.children.length; i++) {
            if (chatLobbyConvosTabs.children[i].id.substr(0, 4) === 'tab-') {
                count++;
            }
        }
        return count;
    }

    /**
     * @returns { Array }
     */
    function findImConvoRoomIds() {
        var chatLobbyConvosTabs = document.getElementById('chat-lobby-convos-tabs');
        var rooms = [], i;
        if (!chatLobbyConvosTabs) {
            for (i in window.openedPopups) {
                if (i.substr(0, 5) === 'room_') {
                    rooms.push(parseInt(i.substr(5)));
                }
            }
            return rooms;
        }
        for (i = 0; i < chatLobbyConvosTabs.children.length; i++) {
            if (chatLobbyConvosTabs.children[i].id.substr(0, 4) === 'tab-') {
                rooms.push(parseInt(chatLobbyConvosTabs.childNodes[i].id.substr(4)));
            }
        }
        return rooms;
    }

    function closeChatConversation(roomId) {
        var isPopup = (document.body.classList.contains('sitewide-im-popup-body'));
        /*{+START,IF,{$OR,{$NOT,{$ADDON_INSTALLED,cns_forum}},{$NOT,{$CNS}}}}*/
        $cms.ui.generateQuestionUi(
            '{!chat:WANT_TO_DOWNLOAD_LOGS*;^}',
            { 'buttons/cancel': '{!INPUTSYSTEM_CANCEL*;^}', 'buttons/yes': '{!YES*;^}', 'buttons/no': '{!NO*;^}' },
            '{!chat:CHAT_DOWNLOAD_LOGS*;^}',
            null,
            function (logs) {
                if (logs.toLowerCase() !== '{!INPUTSYSTEM_CANCEL*;^}'.toLowerCase()) {
                    if (logs.toLowerCase() === '{!YES*;^}'.toLowerCase()) {
                        window.open('{$FIND_SCRIPT_NOHTTP;,download_chat_logs}?room=' + roomId + '{$KEEP*;^}');
                        deinvolveIm(roomId, true, isPopup);
                        return;
                    }
                    /*{+END}*/
                    deinvolveIm(roomId, false, isPopup);
                    /*{+START,IF,{$OR,{$NOT,{$ADDON_INSTALLED,cns_forum}},{$NOT,{$CNS}}}}*/
                }
            }
        );
        /*{+END}*/
    }

    function deinvolveIm(roomId, logs, isPopup) { // is_popup means that we show a progress indicator over it, then kill the window after deinvolvement
        if (isPopup && document.body) {
            $dom.html(document.body, '<div class="spaced"><div aria-busy="true" class="ajax-loading vertical-alignment"><img width="20" height="20" src="' + $util.srl('{$IMG*;,loading}') + '" alt="{!LOADING;^}" /> <span>{!LOADING;^}</span></div></div>');
        }

        var element, participants = null, tabEl;
        var tabs = document.getElementById('chat-lobby-convos-tabs');
        if (tabs) {
            element = document.getElementById('room-' + roomId);
            if (!element) { // Probably already been clicked once, lag
                return;
            }

            tabEl = document.getElementById('tab-' + roomId);
            element.style.display = 'none';
            tabEl.style.display = 'none';

            participants = tabEl.participants;
        } else {
            if (isPopup) {
                participants = ((window.alreadyAutonomous !== undefined) && (window.alreadyAutonomous)) ? window.participants : window.topWindow.openedPopups['room_' + roomId].participants;
            }
        }

        window.topWindow.alreadyReceivedRoomInvites[roomId] = false;
        if (isPopup) {
            window.isShutdown = true;
        }

        setTimeout(function () { // Give time for any logs to download (download does not need to have finished - but must have loaded into a request response on the server side)
            window.topWindow.$cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,messages}?action=deinvolve_im' + window.topWindow.$cms.keep(), null, 'room_id=' + encodeURIComponent(roomId)); // Has to be on topWindow or it will be lost if the window was explicitly closed (it is unloading mode and doesn't want to make a new request)

            if (participants) {
                window.topWindow.allConversations[participants] = null;
            }

            if (tabs) {
                if (element && (element.parentNode)) {
                    element.remove();
                }
                if (!tabEl.parentNode) {
                    return;
                }

                tabEl.remove();

                // All gone?
                var count = Number(countImConvos());
                if (count === 0) {
                    $dom.html(tabs, '&nbsp;');
                    document.getElementById('chat-lobby-convos-tabs').style.display = 'none';
                    $dom.html('#chat-lobby-convos-areas', window.noImHtml);
                    if (document.getElementById('invite-ongoing-im-button')) {
                        document.getElementById('invite-ongoing-im-button').disabled = true;
                    }
                } else {
                    chatSelectTab(document.getElementById('tab-' + findImConvoRoomIds().pop()));
                }
            } else if (isPopup) {
                window.onbeforeunload = null;
                window.close();
            }
        }, logs ? window.LOGS_DOWNLOAD_INTERVAL : 10);
    }

    function findCurrentImRoom() {
        var chatLobbyConvosTabs = document.getElementById('chat-lobby-convos-tabs');
        if (!chatLobbyConvosTabs) {
            return window.roomId;
        }
        for (var i = 0; i < chatLobbyConvosTabs.children.length; i++) {
            if ((chatLobbyConvosTabs.children[i].localName === 'div') && (chatLobbyConvosTabs.children[i].classList.contains('chat-lobby-convos-current-tab'))) {
                return parseInt(chatLobbyConvosTabs.childNodes[i].id.substr(4));
            }
        }
        return null;
    }

    function chatSelectTab(tabEl) {
        var i, chatLobbyConvosTabs = document.getElementById('chat-lobby-convos-tabs');

        for (i = 0; i < chatLobbyConvosTabs.children.length; i++) {
            if (chatLobbyConvosTabs.children[i].classList.contains('chat-lobby-convos-current-tab')) {
                chatLobbyConvosTabs.children[i].className = ((chatLobbyConvosTabs.children[i].classList.contains('chat-lobby-convos-tab-first')) ? 'chat-lobby-convos-tab-first ' : '') + 'chat-lobby-convos-tab-uptodate';
                document.getElementById('room-' + chatLobbyConvosTabs.children[i].id.substr(4)).style.display = 'none';
                break;
            }
        }

        var tabId = tabEl.id.substr(4); // Remove leading 'tab-'

        document.getElementById('room-' + tabId).style.display = 'block';
        try {
            document.getElementById('post_' + tabId).focus();
        } catch (ignore) {}

        tabEl.className = ((tabEl.classList.contains('chat-lobby-convos-tab-first')) ? 'chat-lobby-convos-tab-first ' : '') + 'chat-lobby-convos-tab-uptodate chat-lobby-convos-current-tab';
    }
}(window.$cms, window.$util, window.$dom));
