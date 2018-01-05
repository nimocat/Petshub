<?php

if (!defined('ABSPATH')) exit;

$_plugins = array(
    array(
        "code" => "gd-bbpress-toolbox",
        "name" => "GD bbPress Toolbox",
        "description" => "Expand bbPress powered forums with attachments upload, BBCodes support, signatures, widgets, quotes, toolbar menu, activity tracking, enhanced widgets, extra views...",
        "punchline" => "Enhancing WordPress forums powered by bbPress",
        "color" => "#224760",
    ),
    array(
        "code" => "gd-clever-widgets",
        "name" => "GD Clever Widgets",
        "description" => "A collection of sidebars widgets for unit conversion, advanced navigation, QR Code, videos, posts and authors information, enhanced versions of default widgets and more.",
        "punchline" => "Powerful widgets to enhance your website",
        "color" => "#744D08",
    ),
    array(
        "code" => "gd-content-tools",
        "name" => "GD Content Tools",
        "description" => "Register and control custom post types and taxonomies. Powerful meta fields and meta boxes management. Extra widgets, custom rewrite rules, enhanced features...",
        "punchline" => "Enhancing WordPress Content Management",
        "color" => "#AD0067",
    ),
    array(
        "code" => "gd-crumbs-navigator",
        "name" => "GD Crumbs Navigator",
        "description" => "Breadcrumbs based navigation, fully responsive and customizable, supporting post types, all types of archives, 404 pages, search results and third-party plugins.",
        "punchline" => "Improve your website navigation with Breadcrumbs",
        "color" => "#0CA991",
    ),
    array(
        "code" => "gd-knowledge-base",
        "name" => "GD Knowledge Base",
        "description" => "Complete knowledge base system supporting all themes, with different content types, FAQ, products, live search, feedbacks and ratings, built-in analytics and more.",
        "punchline" => "The knowledge base plugin you have been waiting for",
        "color" => "#3c6d29",
    ),
    array(
        "code" => "gd-press-tools",
        "name" => "GD Press Tools",
        "description" => "Collection of various administration, backup, cleanup, debug, events logging, tweaks and other useful tools and addons that can help with everyday tasks and optimization.",
        "punchline" => "Powerful administration plugin for WordPress",
        "color" => "#333333",
    ),
    array(
        "code" => "gd-rating-system",
        "name" => "GD Rating System",
        "description" => "Powerful, highly customizable and versatile ratings plugin to allow your users to vote for anything you want. Includes different rating methods and add-ons.",
        "punchline" => "Ultimate rating plugin for WordPress",
        "color" => "#262261",
    ),
    array(
        "code" => "gd-security-toolbox",
        "name" => "GD Security Toolbox",
        "description" => "A collection of many security related tools for .htaccess hardening with security events log, ReCaptcha, firewall, and tweaks collection, login and registration control and more.",
        "punchline" => "Proactive protection and security hardening",
        "color" => "#6F1A1A",
    ),
    array(
        "code" => "gd-seo-toolbox",
        "name" => "GD SEO Toolbox",
        "description" => "Toolbox plugin with a number of search engine optimization related modules for Sitemaps, Robots.txt, Robots Meta and Knowledge Graph control, with more modules to be added.",
        "punchline" => "Search Engine Optimization for WordPress",
        "color" => "#C65C0F",
    ),
    array(
        "code" => "gd-swift-navigator",
        "name" => "GD Swift Navigator",
        "description" => "Add Swift, powerful and easy to use navigation control in the page corner with custom number of action buttons, popup navigation menus or custom HTML content.",
        "punchline" => "Swift, powerful and easy to use Navigation",
        "color" => "#0773B7",
    ),
    array(
        "code" => "gd-topic-polls",
        "name" => "GD Topic Polls",
        "description" => "Implements polls system for bbPress powered forums, where users can add polls to topics, with a wide range of settings to control voting, poll closing, display of results and more.",
        "punchline" => "Enhance bbPress forums with topic polls",
        "color" => "#01665e",
    ),
    array(
        "code" => "gd-topic-prefix",
        "name" => "GD Topic Prefix",
        "description" => "Implements topic prefixes system, with support for styling customization, forum specific prefix groups with use of user roles, default prefixes, filtering of topics by prefix and more.",
        "punchline" => "Easy to use topic prefixes for bbPress forums",
        "color" => "#A10A0A",
    ),
    array(
        "code" => "gd-webfonts-toolbox",
        "name" => "GD WebFonts Toolbox",
        "description" => "An easy way to add Web Fonts (Google, Adobe, Typekit) and local FontFaces to standard and custom CSS selectors, with WordPress editor integration and more.",
        "punchline" => "Easy and powerful Web fonts integration",
        "color" => "#6F0392",
    )
);

?>

<div class="d4p-about-dev4press-plugins">
    <?php foreach ($_plugins as $plugin) { 
        $_url = 'https://plugins.dev4press.com/'.$plugin['code'].'/'; ?>
        
    <div class="d4p-dev4press-plugin">
        <div class="_badge" style="background-color: <?php echo $plugin['color']; ?>;">
            <a href="<?php echo $_url; ?>" target="_blank"><i class="d4p-icon d4p-plugin-icon-<?php echo $plugin['code']; ?>"></i></a>
        </div>
        <div class="_info">
            <h5><a href="<?php echo $_url; ?>" target="_blank"><?php echo $plugin['name']; ?></a></h5>
            <em><?php echo $plugin['punchline']; ?></em>
            <p><?php echo $plugin['description']; ?></p>
        </div>
    </div>

    <?php } ?>
</div>