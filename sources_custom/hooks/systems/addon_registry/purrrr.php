<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    purrrr
 */

// Note to developers: this doesn't install at Composr installation due to install order. Who'd want it as a bundled addon though?

/**
 * Hook class.
 */
class Hook_addon_registry_purrrr
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_md5.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11'; // addon_version_auto_update ee9dd94b204867ff349ac12e4cbdf64b
    }

    /**
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }

    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return 11.9;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Fun and Games';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Kamen Blaginov';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as ' . brand_name();
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Populate your galleries with 40 LOLCAT images. Meow.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [
                'galleries',
            ],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
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
            'data_custom/images/lolcats/funny-pictures-ridiculous-poses-moddles.jpg',
            'data_custom/images/lolcats/index.html',
            'sources_custom/hooks/systems/addon_registry/purrrr.php',
        ];
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
     * @param  ?float $upgrade_major_minor From what major/minor version we are upgrading (null: new install)
     * @param  ?integer $upgrade_patch From what patch version of $upgrade_major_minor we are upgrading (null: new install)
     */
    public function install(?float $upgrade_major_minor = null, ?int $upgrade_patch = null)
    {
        if (!module_installed('galleries')) {
            return;
        }

        if ($upgrade_major_minor === null) {
            require_lang('galleries');
            require_code('galleries2');

            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-has-trophy-wife.jpg', 'TROPHY WIFE', 'TROPHY WIFE', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-is-on-steroids.jpg', 'STREROIDS', 'STREROIDS', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-is-a-hoarder.jpg', 'Tonight on A&E', 'Tonight on A&E', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-winks-at-you.jpg', 'Hey there', 'Hey there', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-does-not-see-your-point.jpg', '...your point?', '...your point?', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-asks-you-to-pay-fine.jpg', 'just pay the fine', 'just pay the fine', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-is-a-people-lady.jpg', 'Walter never showed up ...', 'Walter never showed up ...', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-looks-like-a-vase.jpg', 'Feline Dynasty', 'Feline Dynasty', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-can-poop-rainbows.jpg', 'And I can poop dem too ...', 'And I can poop dem too ...', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-does-math.jpg', 'You and your wife have 16 kittens ...', 'You and your wife have 16 kittens ...', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-kittens-dispose-of-boyfriend.jpg', 'Itteh Bitteh Kitteh Boyfriend Disposal Committeh', 'Itteh Bitteh Kitteh Boyfriend Disposal Committeh', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-has-had-fun.jpg', 'Now DAT', 'Now DAT', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-kitten-tries-to-stay-neutral.jpg', 'Mah bottom is twyin to take ovuh', 'Mah bottom is twyin to take ovuh', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-decides-what-to-do.jpg', 'Crap! Here he comes...!', 'Crap! Here he comes...!', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-looks-like-boots.jpg', 'GET GLASSES!', 'GET GLASSES!', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cats-have-war.jpg', 'How wars start.', 'How wars start.', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-is-stuck-in-drawer.jpg', 'Dog can\'t take a joke.', 'Dog can\'t take a joke.', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-kitten-drops-a-nickel-under-couch.jpg', 'I drop a nikel under der.', 'I drop a nikel under der.', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-asks-you-for-a-favor.jpg', 'Do me a favor', 'Do me a favor', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-kitten-fixes-puppy.jpg', 'I fix puppy so now he listen.', 'I fix puppy so now he listen.', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-is-very-comfortable.jpg', 'i is sooooooooo comfurbals...', 'i is sooooooooo comfurbals...', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-kitten-ends-meeting2.jpg', 'This meeting is over.', 'This meeting is over.', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cats-are-in-a-musical.jpg', 'When you\'re a cat ...', 'When you\'re a cat ...', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-hates-your-tablecloth.jpg', 'No, thanks.', 'No, thanks.', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-eyes-steak.jpg', 'is it dun yet?', 'is it dun yet?', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-basement-cat-has-pink-sheets.jpg', 'PINK??!', 'PINK??!', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-kittens-yell-at-eachother.jpg', 'WAIT YOUR TURN!', 'WAIT YOUR TURN!', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-sits-in-box.jpg', 'Sittin in ur mails', 'Sittin in ur mails', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-has-a-beatle.jpg', 'GEORGE', 'GEORGE', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-sits-on-your-laptop.jpg', 'Rebutting ...', 'Rebutting ...', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-has-a-close-encounter.jpg', 'CLOSE  ENCOUNTRES ...', 'CLOSE  ENCOUNTRES ...', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-ridiculous-poses-moddles.jpg', 'Ridiculous poses', 'Ridiculous poses', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-is-a-doctor.jpg', 'Dr. House cat...', 'Dr. House cat...', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-pounces-on-deer.jpg', 'National Geographic', 'National Geographic', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-fish-and-cat-judge-your-outfit.jpg', 'Bad outfit', 'Bad outfit', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-comes-to-save-day.jpg', 'Here I come to save the day!!', 'Here I come to save the day!!', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-ok-captain-obvious.jpg', 'Okay, Captain Obvious', 'Okay, Captain Obvious', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-kermit-was-about.jpg', 'Kermit makes a discovery', 'Kermit makes a discovery', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-ai-calld-jenny-craig.jpg', 'Jenny Craig', 'Jenny Craig', '');
            $this->add_image('data_custom/images/lolcats/funny-pictures-cat-special-delivery.jpg', 'Special Delivery', 'Special Delivery', '');
        }
    }

    public function add_image($url = '', $title = '', $description = '', $notes = '')
    {
        add_image($title, 'root', $description, $url, 1, 1, 1, 1, $notes, db_get_first_id());
    }
}
