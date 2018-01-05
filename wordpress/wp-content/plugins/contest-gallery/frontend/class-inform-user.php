<?php		

//$idtest = 1;

					
				/*class Content {  
				
				public $headers1;
				
				function __construct($headers1){$this->headers1=$headers1;}
		
				function rw_change_email_headers(){
						
				$this -> headers1 = 'Content-type: text/html; charset=iso-8859-1';
				return $headers1;
				
					}
					
					}*/
					
					
									function new_mail_from($from) {
									
					@$galeryID = @$_POST['GalleryID'];
									
					global $wpdb;
					$tablenameemail = $wpdb->prefix . "contest_gal1ery_mail";	
					$selectSQLemail = $wpdb->get_row( "SELECT * FROM $tablenameemail WHERE GalleryID = $galeryID" );
					
					$Reply = $selectSQLemail->Reply;
					
				return $Reply;	
				}
				
				
				function new_mail_from_name($from) {
				
					@$galeryID = @$_POST['GalleryID'];
									
					global $wpdb;
					$tablenameemail = $wpdb->prefix . "contest_gal1ery_mail";	
					$selectSQLemail = $wpdb->get_row( "SELECT * FROM $tablenameemail WHERE GalleryID = $galeryID" );
					
					$Admin = $selectSQLemail->Admin;
			    
				
				 return $Admin;
				}
					
				@$headers["From"] = $from;	
				
				//echo $headers["From"];
					
				
		
				/*class Blogname {  
				
				public $blogname;

				function __construct($blogname){$this->blogname=$blogname;}
								
	 
				function new_mail_from_name() {
				$this -> blogname = 'Your Blog Name';
				return $blogname;
				}
				
				}*/
				
				

				
				
				
				
									/*add_filter( 'wp_mail', 'rw_change_email_headers1' );
				function rw_change_email_headers1( $headers )
					{
				$headers['From'] = 'From: Test3';
				return $headers;
					}*/
			
					
		?> 