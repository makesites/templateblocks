-- Template Blocks: Setup SQL
-- 
-- Database: `template_blocks`
-- 
-- --------------------------------------------------------
-- 
-- Table structure for table `blocks`
-- 
CREATE TABLE IF NOT EXISTS `{{TABLE_PREFIX}}blocks` ( `id` int(11) NOT NULL auto_increment, `title` varchar(50) NOT NULL, `type` varchar(50) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- 
-- Dumping data for table `blocks`
-- 
INSERT INTO `{{TABLE_PREFIX}}blocks` (`id`, `title`, `type`) VALUES (1, 'header', 'HTML'), (2, 'topbar', 'PHP'), (3, 'sidebar', 'HTML'), (4, 'footer', 'HTML'), (5, 'styles', 'CSS'), (6, 'additional head', 'HTML');
-- --------------------------------------------------------
-- 
-- Table structure for table `sections`
-- 
CREATE TABLE IF NOT EXISTS `{{TABLE_PREFIX}}sections` ( `id` int(11) NOT NULL auto_increment, `title` varchar(50) NOT NULL, `slug` varchar(50) NOT NULL, `template` varchar(10) NOT NULL, `content` longtext NOT NULL, `position` varchar(4) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- 
-- Dumping data for table `sections`
-- 
INSERT INTO `{{TABLE_PREFIX}}sections` (`id`, `title`, `slug`, `template`, `content`, `position`) VALUES (1, 'Home page', 'index', '1', '<h2>A Sample Title</h2>\n\n<p>Welcome to your new website. This is some sample content entered just to jump-start in creating your website. Sorry if it looks too plain for your taste but the intention is to make it as easier to customize as possible.</p>\n\n<p>You will find some basic styles in the styles block that you can alter to your liking and there are also some other blocks there that create the tempalte you are viewing.</p>\n\n<p><a href="$Template_dir/admin.php">Login now to your administration and start editing</a></p>\n\n<p>More dummy text follows...</p>\n\n<p>Nulla mattis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut sem orci, egestas sed, pellentesque at, tempus ut, dolor. Aenean molestie feugiat tortor. Praesent in augue nec odio adipiscing consequat. Suspendisse ut nibh id mi venenatis vulputate. Nullam felis leo, faucibus eget, ullamcorper eu, porttitor et, felis. Aenean aliquam, arcu eu placerat elementum, ante sapien tempus quam, vitae feugiat est magna sed risus. Aenean elit nisl, rhoncus a, varius fermentum, congue nec, dui. Vestibulum lacus velit, auctor non, porttitor cursus, euismod non, ligula. Nullam dolor neque, scelerisque vitae, aliquam eget, porta quis, libero. Praesent diam quam, tempor non, ornare id, iaculis a, nisl.</p>\n\n<p>Proin ultricies ullamcorper nisl. Sed consequat rutrum turpis. Ut consectetuer orci scelerisque urna volutpat elementum. Aliquam interdum erat. Suspendisse tristique tempor ligula. Sed lobortis est sit amet massa. Nulla facilisi. Cras feugiat luctus sapien. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Ut elit pede, sollicitudin dignissim, fringilla a, facilisis ut, felis. Pellentesque nec velit. Donec lacus nulla, egestas ut, condimentum quis, volutpat non, dui. Praesent varius. In hac habitasse platea dictumst. Curabitur vitae odio. Maecenas risus. Maecenas vitae elit. Curabitur vitae leo eget justo convallis dapibus. In pede. </p>', '0(0)'), (2, 'About Us', 'about', '1', 'Sample text for the "about us" page', '0(1)'), (3, 'Contact', 'contact', '1', 'contact form...', '0(2)');
-- --------------------------------------------------------
-- 
-- Table structure for table `templates`
-- 
CREATE TABLE IF NOT EXISTS `{{TABLE_PREFIX}}templates` ( `id` int(11) NOT NULL auto_increment, `title` varchar(50) NOT NULL, `type` varchar(200) NOT NULL, `blocks` varchar(200) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- 
-- Dumping data for table `templates`
-- 
INSERT INTO `{{TABLE_PREFIX}}templates` (`id`, `title`, `type`, `blocks`) VALUES (1, 'Main', 'XHTML 1.0 Transitional', '5|6#1|2|3|X|4');
