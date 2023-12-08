<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    health_check
 */

/**
 * Hook class.
 */
class Hook_addon_registry_health_check
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
        return 'New Features';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
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
        return 'The Health Check addon automatically finds problems on your website and server, with over 100 checks.

On the modern advanced web there is too much to keep in mind to check. All kinds of things could go wrong without you noticing, which could be embarrassing.
For example, what if:
1) Your outgoing e-mail goes down, breaking sign-ups
2) You forget to renew SSL
3) You forget to renew your domain name
4) A hacker takes control of the domain name and puts up a fake site
5) You accidentally block Google from accessing the website
All the above scenarios are auto-detectable.
In fact, you can detect problems stemming from many kinds of situation, including:
 - Quality issues after building a new site or theme
 - Software compatibility issues
 - Problems after a Composr upgrade
 - Hardware failure
 - Configuration issues
 - Lack of server capacity
 - Hack-attacks
 - Lack of routine maintenance or website up-keep

[title="2"]Operation[/title]

The Health Check can be run manually, or regularly run in the background (requires CRON to be set up and for you to enable the "Health Check results" notification).

[title="2"]Check sections[/title]

At the time of writing the list of check sections is:
 - Backups \ Backups
 - Bloated data \ Directory size
 - Bloated data \ Log size
 - Bloated data \ Table size
 - Build mistakes \ Broken links
 - Build mistakes \ Guest access
 - Build mistakes \ Incomplete content
 - Build mistakes \ Local linking
 - Build mistakes \ Manual checks for web standards
 - CRON \ CRON set up
 - CRON \ Slow CRON
 - Deployment mistakes \ Site open-status
 - Domains \ DNS resolution
 - Domains \ Domain expiry
 - E-mail \ E-mail configuration
 - E-mail \ E-mail operation
 - E-mail \ E-mail queue
 - E-mail \ SMTP blacklisting
 - E-mail \ SPF
 - Hack-attacks \ Attack frequency
 - Hack-attacks \ Failed logins
 - Hack-attacks \ Overseas access
 - Hack-attacks \ Rate-limit spiking
 - Installation environment \ PHP functionality etc
 - Marketing \ Analytics
 - Marketing \ Google Analytics
 - Marketing \ Social media
 - Network \ External access
 - Network \ Packet loss
 - Network \ Transfer latency
 - Network \ Transfer speed
 - Newsletter \ Newsletter queue
 - Performance \ 404 pages
 - Performance \ Cookies
 - Performance \ Manual performance checks
 - Performance \ Page speed
 - robots.txt \ robots.txt completeness
 - robots.txt \ robots.txt correctness
 - robots.txt \ robots.txt validity
 - robots.txt \ Sitemap linkage
 - Security \ Directory securing
 - Security \ Malware
 - Security \ Site orphaning
 - SEO \ H1 tags
 - SEO \ Manual SEO checks
 - SEO \ Meta description
 - SEO \ Meta keywords
 - SEO \ Page titles
 - SEO \ XML Sitemap
 - Server performance \ CPU load
 - Server performance \ Disk space
 - Server performance \ Hanging processes
 - Server performance \ I/O load
 - Server performance \ RAM
 - Server performance \ Server uptime
 - Software integrity \ Database corruption
 - Software integrity \ Database integrity
 - Software integrity \ File integrity
 - Software integrity \ File permissions integrity
 - Software integrity \ Upgrade completion
 - SSL \ Insecure embedding
 - SSL \ Insecure linking
 - SSL \ SSL correctness
 - SSL \ SSL grading
 - Stability \ Block integrity
 - Stability \ Error log
 - Stability \ Manual stability checks
 - Stability \ Page integrity
 - Upkeep \ Admin account staleness
 - Upkeep \ Composr version
 - Upkeep \ Copyright date
 - Upkeep \ PHP version
 - Upkeep \ Staff checklist
 - User-experience for mistakes \ 404 pages
 - User-experience for mistakes \ HTTPS redirection
 - User-experience for mistakes \ www redirection

[title="2"]Configuration[/title]

There are a large number of configuration options available, under Admin Zone > Setup > Configuration > Health Check.
This includes:
 - setting which pages to do deep scans of
 - calibrating the checks (setting thresholds)
 - configuring background ("CRON") checks, including the frequency, and which check sections to run

[title="2"]What if things break too badly for the Health Check to run?[/title]

There are a couple of approaches you can take to make sure you know if the health checker is itself down:
1) Feed an uptime checker into [tt]data_custom/health_check.php[/tt] -- if it gives an HTTP error or the server does not respond, then you know Health Checks do not run (and a none-blank result shows there is a failing health check)
2) Enable the "Send full reports" option and check you receive the report each day (either manually or using some kind of third party e-mail scanning tool)

[title="2"]Philosophy[/title]

There is a vast amount to check when it comes to web development. Even though we check over 100 things, we can not realistically check everything under the scope of the Health Check.
The Health Check focuses on issues likely faced by end-users, and assumes Composr itself has been well programmed and tested.

The Composr ecosystem also has:
 - Unit testing. This is used for testing a huge array of low-level coding issues (too detailed for Health Check, possibly destructive, and requiring a lot of testing framework code). The tests are regularly run prior to Composr releases being made by the developers.
 - Web standards checks (often called "Validation"). These checks are too detailed to be done within the Health Check (and any individual mistake is likely not going to hurt your site in practice). Composr has web standards checking functionality built in, which can be accessed from the default theme\'s footer and when previewing content (depending on configuration). We also have manual health checks linking out to 3rd party validators.
 - PHP info. The Health Check is not intended as a way to report on all your settings, only problematic ones. If you want to list some information about the PHP environment, use Admin Zone > Tools > PHP info to do this.
 - Cleanup tools. The Health Check does not attempt to "cleanup" caches and so on, only check for problems. The cleanup tools can be accessed from Admin Zone > Tools > Website cleanup tools.
 - Staff checklist. The staff checklist (on the Admin Zone dashboard) has detailed information on staff actions that need performing. There is integration of this into the Health Check, but only at a high level.
 - External scanning tools. The Health Check doesn\'t itself directly ensure your website is perfectly optimised, it just focuses on the more major issues, and does sign-posting to other tools. The Health Check will link to various external tools that can perform detailed scans, such as gauging your SSL security.
';
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
            'requires' => array(),
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
        return 'themes/default/images_custom/icons/48x48/menu/adminzone/tools/health_check.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/health_check.php',
            'lang_custom/EN/health_check.ini',
            'sources_custom/hooks/systems/config_categories/index.html',
            'sources_custom/hooks/systems/config_categories/.htaccess',
            'sources_custom/hooks/systems/config_categories/health_check.php',
            'sources_custom/hooks/systems/health_checks/.htaccess',
            'sources_custom/hooks/systems/health_checks/index.html',
            'sources_custom/hooks/systems/health_checks/install_environment.php',
            'sources_custom/hooks/systems/health_checks/cron.php',
            'sources_custom/hooks/systems/health_checks/domains.php',
            'sources_custom/hooks/systems/health_checks/email.php',
            'sources_custom/hooks/systems/health_checks/email_newsletter.php',
            'sources_custom/hooks/systems/health_checks/integrity.php',
            'sources_custom/hooks/systems/health_checks/marketing.php',
            'sources_custom/hooks/systems/health_checks/marketing_seo.php',
            'sources_custom/hooks/systems/health_checks/marketing_seo_robotstxt.php',
            'sources_custom/hooks/systems/health_checks/mistakes_build.php',
            'sources_custom/hooks/systems/health_checks/mistakes_deploy.php',
            'sources_custom/hooks/systems/health_checks/mistakes_user_ux.php',
            'sources_custom/hooks/systems/health_checks/network.php',
            'sources_custom/hooks/systems/health_checks/performance.php',
            'sources_custom/hooks/systems/health_checks/performance_bloat.php',
            'sources_custom/hooks/systems/health_checks/performance_server.php',
            'sources_custom/hooks/systems/health_checks/security.php',
            'sources_custom/hooks/systems/health_checks/security_hackattack.php',
            'sources_custom/hooks/systems/health_checks/security_ssl.php',
            'sources_custom/hooks/systems/health_checks/stability.php',
            'sources_custom/hooks/systems/health_checks/upkeep.php',
            'sources_custom/hooks/systems/health_checks/upkeep_backups.php',
            'themes/default/images_custom/icons/24x24/menu/adminzone/tools/health_check.png',
            'themes/default/images_custom/icons/24x24/menu/adminzone/tools/index.html',
            'themes/default/images_custom/icons/48x48/menu/adminzone/tools/health_check.png',
            'themes/default/images_custom/icons/48x48/menu/adminzone/tools/index.html',
            'sources_custom/health_check.php',
            'sources_custom/hooks/systems/config/hc_admin_stale_threshold.php',
            'sources_custom/hooks/systems/config/hc_compound_requests_per_second_threshold.php',
            'sources_custom/hooks/systems/config/hc_compound_requests_window_size.php',
            'sources_custom/hooks/systems/config/hc_cpu_pct_threshold.php',
            'sources_custom/hooks/systems/config/hc_transfer_speed_threshold.php',
            'sources_custom/hooks/systems/config/hc_cron_threshold.php',
            'sources_custom/hooks/systems/config/hc_disk_space_threshold.php',
            'sources_custom/hooks/systems/config/hc_error_log_day_flood_threshold.php',
            'sources_custom/hooks/systems/config/hc_google_safe_browsing_api_key.php',
            'sources_custom/hooks/systems/config/hc_io_pct_threshold.php',
            'sources_custom/hooks/systems/config/hc_mail_address.php',
            'sources_custom/hooks/systems/config/hc_mail_password.php',
            'sources_custom/hooks/systems/config/hc_mail_server.php',
            'sources_custom/hooks/systems/config/hc_mail_server_port.php',
            'sources_custom/hooks/systems/config/hc_mail_server_type.php',
            'sources_custom/hooks/systems/config/hc_mail_username.php',
            'sources_custom/hooks/systems/config/hc_mail_wait_time.php',
            'sources_custom/hooks/systems/config/hc_page_size_threshold.php',
            'sources_custom/hooks/systems/config/hc_page_speed_threshold.php',
            'sources_custom/hooks/systems/config/hc_process_hang_threshold.php',
            'sources_custom/hooks/systems/config/hc_processes_to_monitor.php',
            'sources_custom/hooks/systems/config/hc_ram_threshold.php',
            'sources_custom/hooks/systems/config/hc_requests_per_second_threshold.php',
            'sources_custom/hooks/systems/config/hc_requests_window_size.php',
            'sources_custom/hooks/systems/config/hc_scan_page_links.php',
            'sources_custom/hooks/systems/config/hc_transfer_latency_threshold.php',
            'sources_custom/hooks/systems/config/hc_uptime_threshold.php',
            'sources_custom/hooks/systems/config/hc_cron_notify_regardless.php',
            'sources_custom/hooks/systems/config/hc_cron_regularity.php',
            'sources_custom/hooks/systems/config/hc_cron_sections_to_run.php',
            'sources_custom/hooks/systems/config/hc_is_test_site.php',
            'sources_custom/hooks/systems/cron/_health_check.php',
            'sources_custom/hooks/systems/notifications/health_check.php',
            'themes/default/templates_custom/HEALTH_CHECK_RESULTS.tpl',
            'themes/default/templates_custom/HEALTH_CHECK_SCREEN.tpl',
            'data_custom/health_check.php',
            'adminzone/pages/modules_custom/admin_health_check.php',
            'sources_custom/hooks/systems/page_groupings/health_check.php',
        );
    }
}
