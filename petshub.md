<center>![img](img/logo2.png)</center>



## Discription

[Petshub](139.199.29.220) is a blogging website based on wordpress, aimed at providing a platform for pets lovers to share their stories and communicate with each other. Functions like adding posts, searching and sorting, giving comments and scores, and adding favorites are provided. Although it doesn't have a domain name, you can visit it by clicking [Petshub](139.199.29.220).

## Features

### Functions
####  Cat list managment
- [x] the "BLOG" part on the home page shows the cats list with images and title
- [x] browse the detailed information
####  Sorting support
- [x] the "Catagory" button on the navigation bar will lead you to the sorting page
- [x] click on different cat breeds(shown in bold red letters) to view related posts
- [x] browse the detailed information
#### Searching support
- [x] the "Catagory" button on the navigation bar will lead you to the searching page
- [x] search bar are shown on the right part of the page
- [x] input keywords(cat breeds, post title, cat name,etc.) then searching results are shown on the left side
- [x] click on the "favorite" button under the image to add it into your favorites
####  Detailed information browsing
- [x] view the detail page to vote or give comments by clicking on the cat images
- [x] do it after "Cat list management" and "Sorting support" functions
####  Comments
- [x] give your comment in the "COMMENT" part under the posts
- [x] it is only valid for loggedin users
####  "Like" and "Stars"
- [x] click on the "favorite" button under the posts to add it into your favorites
- [x] fill in different numbers of stars(0-5) to give post a score
####  Score algorithm
- [x] average all the star-rating scores to give a final mark
####  Database
- [x] Log into [Petshub Database](139.199.29.220:8081/phpMyAdmin/) with username:1fVa81mZ, and password:k8Tvt4MPtA3V
#### Administration
- [x] Log into [Petshub Dashboard](139.199.29.220/wp-adin) with username:admin, and password:12341234
### Entity-Relationship diagram
#### Entities
 - User
 - Administrator
 - Post
 - Comment(weak entity)
 - Score(weak entity)
####  Attributes
 - User: <u>ID</u>, user_name, user_password, user_email, display_name
 - Administrator: <u>ID</u>, name, password, email, display_name
  - Post: <u>ID</u>, tag, author, title+content, publish_time, comment_count
  - Comment: <u>ID</u>, post_id, author, content, publish_time, type
  - Score: <u>ID</u>, post_id, action_key, action_value, publish_time, status
  ####  Relationship
  - User - Publish/Search/Modify - Post
  - User - Publish - Score/Comment
  - Post - Has - Score/Comment
  - Administrator - Manage - User/Post
  #### SQL Statements
  -  Schema related statements
  -  Customer operation related statemenets
  -  Administrator operation related statements
### Webpage Navigation diagram
  ### Page list with description
####  HOME page
- NAVIGATION bar,providing quik link to other pages
     - WELCOME part
     - BLOG part: showing post images linking to detail page
     - GALLORY part: showing several best images chosen by administrator
     - ABOUT part: some description about the website
     - CONTACT part: information of the authors
       #### Register page
     - some information should be submitted before getting an account
     - user_name, real_name, e-mail_address, password, password_recheck
     - click on "REGISTER" button to move on, click on "LOGIN" button to return
       #### Login page
     - fill in user_name or e-mail_address and password
     - login successfully after identification
       #### Logout page
     - come back to HOME page as a loggedout user
       #### Password reset page
     - fill in old_password, new_password and new_password_recheck
     - reset successfully after identification
       #### Account page
     - show user_description, published posts, and published comments
     - each are linked to related detail page
       #### Favorite page
     - show favorite_count and favorite_list
     - each are linked to related detail page
       #### Category & Searching page
     - left part shows different categories(based on cat breeds)
     - right part shows search bar and recommended posts(based on comment count)
     - description of these two functions are shown in "Functions" part(Sorting support and Searching support)
     #### Add a post page
     - add a title for the post
     - upload images by clicking on the button "ADD MEDIA"
     - add description in the blank area
     - choose a special image as the front cover
     - add a tag for the post
     - fill in the answer of elementary math question for human checking
     - click on the "PUBLLISH" button to submit
     - submitted posts will be shown in the BLOG
       #### Gallery page
     - several images are shown in this page
     - each has a message icon and a star icon
     - click on the icons to give comments and give a "like"
       #### Upload photo page
     - fill in title and description
     - upload images by clicking on the button "CHOOSE FILES"
     - click on the "PUBLLISH" button to submit
     - submitted images will be shown in the GALLERY

## Screenshots

<center>![img](img/home.png)</center>

### Gallery

<center>![img](img/gallery.png)</center>

### Detail

<center>![img](img/detail.png)</center>

### Add photos

<center>![img](img/add photo.png)</center>

### User

<center>![img](img/users.png)</center>

## Tech/framework used

### Platform

- built with [Wordpress](https://wordpress.com/)

### Plugins

- AccessPress Anonymous Post
  - support for Sorting Function
- Contest Gallery
  - support for Gallery Function
- Favorites
  - support for "likes" Function
- GD Rating System
  - support for "Stars" Function
- Themify Portfolio Post
  - support for post type 
- Ultimate Member
  - support for User Information​

### Theme

- Themify Ultra(Installation package)
  - set different suitable layouts for different pages
  - choose background images and elegent fonts
  - use the section scrolling function to get dynamic effect


## Diagrams
### Page Flow

<center>![img](img/page.png)</center>

### Transaction Flow Diagram
#### Administrator

<center>![img](img/admin.png)</center>

#### User

<center>![img](img/user.png)</center>

### ER Diagram

<center>![img](img/ER.png)</center>

## Main Code Block
### Automatic scoring system
By registering the hook function, when a new comment is generated, it sends the data to the Tencent NLP interface and returns the score, which is stored in the database according to the meta_key and meta_value: score newly added to the posts. According to the analysis of the contents of the stars in praise, the two scores combined ranking show in the front, the code is as follows:
```php
add_action('comment_post','generate_score');

function generate_score($comment_ID, $comment_approved){
global $wpdb;

if(1 == $comment_approved){//如果评论已经审核
$comment = get_comment($comment_ID);//获取comment对象
$content = $comment->comment_content;//获取content内容
$post_id = $comment->comment_post_ID;//获取帖子ID
//使用sdk连接腾讯云NLP接口
error_reporting(E_ALL ^ E_NOTICE);
require_once './QcloudApi.php';

$config = array('SecretId'        => 'AKIDsb7RaWvoNm13sTf20kGagYCdsuhEcVFj',
'SecretKey'       => 'NULwGK7ev0qlhQu99uQhkYBZifOs7C2d',
'RequestMethod'  => 'POST',
'DefaultRegion'    => 'gz');

$wenzhi = QcloudApi::load(QcloudApi::MODULE_WENZHI, $config);

$package = array("content"=>$content);//将content打包

$a = $wenzhi->TextSentiment($package);//数据包

if ($a === false) {
$error = $wenzhi->getError();
echo "Error code:" . $error->getCode() . ".\n";
echo "message:" . $error->getMessage() . ".\n";
echo "ext:" . var_export($error->getExt(), true) . ".\n";
} else {
$scor = json_decode($a, true);
}
//情感分析打分
$json = $wenzhi->getLastResponse();
$obj = json_decode($json);
$score = $obj->{'negative'} * 100;
wpdb::insert( ‘wp_postmeta’, array( ‘post_id’ => $post_id, ‘meta_key’ => ‘score’, 'meta_value' => $score ), array('%d','%s','%d') );//插入数据库
}
}
```
### Gain score form database:
```php
function get_score( $post_ID ){
$score = $wpdb->get_results("
SELECT 'score' FROM 'wp_postmeta' where 'post_ID'=$_post_ID
");
return $score;
}
```
### Add custom field to the posts:
```SQL
INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
SELECT ID AS post_id, 'score'
AS meta_key '0 AS meta_value
FROM wp_posts WHERE ID NOT IN
(SELECT post_id FROM wp_postmeta WHERE meta_key = 'score')`` AND post_type = 'post';
```

## Links

* [Petshub](139.199.29.220)
* [Readme.md]()
* [Github](https://github.com/nimocat)
* [Project zip]()