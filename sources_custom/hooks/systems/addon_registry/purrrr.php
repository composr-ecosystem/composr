<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    purrrr
 */

// Note to developers: this doesn't install at Composr installation due to install order. Who'd want it as a bundled addon though?

/**
 * Hook class.
 */
class Hook_addon_registry_purrrr
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Fun and Games';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Kamen Blaginov';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array();
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Populate your image of the day gallery with 40 LOLCAT images. After installing the addon select the image of the day in the usual way by going to Admin zone > Content > Images of the day and select the image you want to display.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(
                'iotds',
            ),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/_generic_admin/component.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/purrrr.php',
            'data_custom/images/lolcats/index.html',
            'data_custom/images/lolcats/thumbs/index.html',
            'data_custom/images/lolcats/funny-pictures-basement-cat-has-pink-sheets.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-ai-calld-jenny-craig.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-asks-you-for-a-favor.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-asks-you-to-pay-fine.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-can-poop-rainbows.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-comes-to-save-day.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-decides-what-to-do.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-does-math.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-does-not-see-your-point.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-eyes-steak.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-has-a-beatle.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-has-a-close-encounter.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-has-had-fun.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-has-trophy-wife.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-hates-your-tablecloth.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-is-a-doctor.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-is-a-hoarder.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-is-a-people-lady.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-is-on-steroids.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-is-stuck-in-drawer.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-is-very-comfortable.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-kermit-was-about.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-looks-like-a-vase.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-looks-like-boots.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-ok-captain-obvious.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-pounces-on-deer.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-sits-in-box.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-sits-on-your-laptop.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-special-delivery.jpg',
            'data_custom/images/lolcats/funny-pictures-cat-winks-at-you.jpg',
            'data_custom/images/lolcats/funny-pictures-cats-are-in-a-musical.jpg',
            'data_custom/images/lolcats/funny-pictures-cats-have-war.jpg',
            'data_custom/images/lolcats/funny-pictures-fish-and-cat-judge-your-outfit.jpg',
            'data_custom/images/lolcats/funny-pictures-kitten-drops-a-nickel-under-couch.jpg',
            'data_custom/images/lolcats/funny-pictures-kitten-ends-meeting2.jpg',
            'data_custom/images/lolcats/funny-pictures-kitten-fixes-puppy.jpg',
            'data_custom/images/lolcats/funny-pictures-kitten-tries-to-stay-neutral.jpg',
            'data_custom/images/lolcats/funny-pictures-kittens-dispose-of-boyfriend.jpg',
            'data_custom/images/lolcats/funny-pictures-kittens-yell-at-eachother.jpg',
            'data_custom/images/lolcats/ridiculous_poses_moddles.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-basement-cat-has-pink-sheets.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-ai-calld-jenny-craig.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-asks-you-for-a-favor.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-asks-you-to-pay-fine.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-can-poop-rainbows.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-comes-to-save-day.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-decides-what-to-do.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-does-math.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-does-not-see-your-point.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-eyes-steak.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-has-a-beatle.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-has-a-close-encounter.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-has-had-fun.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-has-trophy-wife.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-hates-your-tablecloth.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-is-a-doctor.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-is-a-hoarder.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-is-a-people-lady.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-is-on-steroids.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-is-stuck-in-drawer.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-is-very-comfortable.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-kermit-was-about.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-looks-like-a-vase.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-looks-like-boots.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-ok-captain-obvious.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-pounces-on-deer.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-sits-in-box.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-sits-on-your-laptop.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-special-delivery.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cat-winks-at-you.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cats-are-in-a-musical.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-cats-have-war.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-fish-and-cat-judge-your-outfit.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-kitten-drops-a-nickel-under-couch.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-kitten-ends-meeting2.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-kitten-fixes-puppy.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-kitten-tries-to-stay-neutral.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-kittens-dispose-of-boyfriend.jpg',
            'data_custom/images/lolcats/thumbs/funny-pictures-kittens-yell-at-eachother.jpg',
            'data_custom/images/lolcats/thumbs/ridiculous_poses_moddles.jpg',
        );
    }

    /**
     * Uninstall the addon.
     */
    public function uninstall()
    {
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install($upgrade_from = null)
    {
        if (!module_installed('iotds')) {
            return;
        }

        if (is_null($upgrade_from)) {
            require_lang('iotds');
            require_code('iotds2');
            require_css('iotds');
            require_code('uploads');
            require_code('images');

            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-has-trophy-wife.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-has-trophy-wife.jpg', 'TROPHY WIFE', 'TROPHY WIFE', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-is-on-steroids.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-is-on-steroids.jpg', 'STREROIDS', 'STREROIDS', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-is-a-hoarder.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-is-a-hoarder.jpg', 'Tonight on A&E', 'Tonight on A&E', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-winks-at-you.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-winks-at-you.jpg', 'Hey there', 'Hey there', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-does-not-see-your-point.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-does-not-see-your-point.jpg', '...your point?', '...your point?', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-asks-you-to-pay-fine.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-asks-you-to-pay-fine.jpg', 'just pay the fine', 'just pay the fine', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-is-a-people-lady.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-is-a-people-lady.jpg', 'Walter never showed up ...', 'Walter never showed up ...', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-looks-like-a-vase.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-looks-like-a-vase.jpg', 'Feline Dynasty', 'Feline Dynasty', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-can-poop-rainbows.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-can-poop-rainbows.jpg', 'And I can poop dem too ...', 'And I can poop dem too ...', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-does-math.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-does-math.jpg', 'You and your wife have 16 kittens ...', 'You and your wife have 16 kittens ...', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-kittens-dispose-of-boyfriend.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-kittens-dispose-of-boyfriend.jpg', 'Itteh Bitteh Kitteh Boyfriend Disposal Committeh', 'Itteh Bitteh Kitteh Boyfriend Disposal Committeh', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-has-had-fun.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-has-had-fun.jpg', 'Now DAT', 'Now DAT', '', 0, 0, 0, 1);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-kitten-tries-to-stay-neutral.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-kitten-tries-to-stay-neutral.jpg', 'Mah bottom is twyin to take ovuh', 'Mah bottom is twyin to take ovuh', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-decides-what-to-do.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-decides-what-to-do.jpg', 'Crap! Here he comes...!', 'Crap! Here he comes...!', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-looks-like-boots.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-looks-like-boots.jpg', 'GET GLASSES!', 'GET GLASSES!', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cats-have-war.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cats-have-war.jpg', 'How wars start.', 'How wars start.', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-is-stuck-in-drawer.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-is-stuck-in-drawer.jpg', 'Dog can\'t take a joke.', 'Dog can\'t take a joke.', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-kitten-drops-a-nickel-under-couch.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-kitten-drops-a-nickel-under-couch.jpg', 'I drop a nikel under der.', 'I drop a nikel under der.', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-asks-you-for-a-favor.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-asks-you-for-a-favor.jpg', 'Do me a favor', 'Do me a favor', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-kitten-fixes-puppy.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-kitten-fixes-puppy.jpg', 'I fix puppy so now he listen.', 'I fix puppy so now he listen.', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-is-very-comfortable.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-is-very-comfortable.jpg', 'i is sooooooooo comfurbals...', 'i is sooooooooo comfurbals...', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-kitten-ends-meeting2.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-kitten-ends-meeting2.jpg', 'This meeting is over.', 'This meeting is over.', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cats-are-in-a-musical.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cats-are-in-a-musical.jpg', 'When you\'re a cat ...', 'When you\'re a cat ...', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-hates-your-tablecloth.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-hates-your-tablecloth.jpg', 'No, thanks.', 'No, thanks.', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-eyes-steak.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-eyes-steak.jpg', 'is it dun yet?', 'is it dun yet?', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-basement-cat-has-pink-sheets.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-basement-cat-has-pink-sheets.jpg', 'PINK??!', 'PINK??!', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-kittens-yell-at-eachother.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-kittens-yell-at-eachother.jpg', 'WAIT YOUR TURN!', 'WAIT YOUR TURN!', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-sits-in-box.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-sits-in-box.jpg', 'Sittin in ur mails', 'Sittin in ur mails', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-has-a-beatle.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-has-a-beatle.jpg', 'GEORGE', 'GEORGE', '', 0, 0, 0, 1);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-sits-on-your-laptop.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-sits-on-your-laptop.jpg', 'Rebutting ...', 'Rebutting ...', '', 0, 0, 0, 1);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-has-a-close-encounter.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-has-a-close-encounter.jpg', 'CLOSE  ENCOUNTRES ...', 'CLOSE  ENCOUNTRES ...', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/ridiculous_poses_moddles.jpg', 'data_custom/images/lolcats/thumbs/ridiculous_poses_moddles.jpg', 'Ridiculous poses', 'Ridiculous poses', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-is-a-doctor.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-is-a-doctor.jpg', 'Dr. House cat...', 'Dr. House cat...', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-pounces-on-deer.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-pounces-on-deer.jpg', 'National Geographic', 'National Geographic', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-fish-and-cat-judge-your-outfit.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-fish-and-cat-judge-your-outfit.jpg', 'Bad outfit', 'Bad outfit', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-comes-to-save-day.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-comes-to-save-day.jpg', 'Here I come to save the day!!', 'Here I come to save the day!!', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-ok-captain-obvious.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-ok-captain-obvious.jpg', 'Okay, Captain Obvious', 'Okay, Captain Obvious', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-kermit-was-about.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-kermit-was-about.jpg', 'Kermit makes a discovery', 'Kermit makes a discovery', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-ai-calld-jenny-craig.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-ai-calld-jenny-craig.jpg', 'Jenny Craig', 'Jenny Craig', '', 0, 0, 0, 0);
            $this->add_iotds('data_custom/images/lolcats/funny-pictures-cat-special-delivery.jpg', 'data_custom/images/lolcats/thumbs/funny-pictures-cat-special-delivery.jpg', 'Special Delivery', 'Special Delivery', '', 0, 0, 0, 0);
        }
    }

    public function add_iotds($url = '', $thumb_url = '', $title = '', $caption = '', $notes = '', $allow_rating = 0, $allow_comments = 0, $allow_trackbacks = 0, $current = 0)
    {
        add_iotd($url, $title, $caption, $thumb_url, $current, $allow_rating, $allow_comments, $allow_trackbacks, $notes, time(), $GLOBALS['FORUM_DRIVER']->get_guest_id());
    }
}
