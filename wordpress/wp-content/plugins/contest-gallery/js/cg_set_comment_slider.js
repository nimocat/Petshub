jQuery(document).ready(function($){
    jQuery( document ).on( 'click', '#cg_slider_comment_submit', function() {


        //alert("cg-comment");

        // var cg_given_rating=$(this).find('a').attr('alt');


        var name = $("#cg_slider_comment_name").val();
        var comment = $("#cg_slider_comment").val();
        var checkID = $("#cg_slider_comment_check").val();
        var email = $("#cg_slider_comment_email").val();
        var timestamp = $("#cg_slider_comment_timestamp").val();
        var cg_galery_id = $('#cg_galeryID').val();

        // Wurde vorher bei show comments weiter gegeben
        var cg_picture_id = $('#cg_slider_comment_picture_id').val();



        //var cg_rate_value = cg_given_rating;
        var cg_ThankYouComment = $('#cg_ThankYouComment').val();


        var countC = parseInt($("#countCommentsQuantity"+cg_picture_id+"").val());


        var cg_slider_comments_elemens_width = $("#cg_slider_comments_elemens_width").val();



        var nameLength = $("#cg_slider_comment_name").val().length;
        var commentLength = $("#cg_slider_comment").val().length;



        // var allowSubmit = 0;

        var allowSubmit = 1;


        //var post_id = jQuery(this).data('id');
        //var post_id = 657567;


        //alert(nameLength);
        //alert(commentLength);


        if($('#cg_i_am_not_a_robot_checkbox.'+checkID+'').is(':checked') ){



            $('#cg_slider_comment_hint_msg').empty();


            if(nameLength<2){

                var cg_comment_name_characters = $('#cg_comment_name_characters').val();
                $('#cg_slider_comment_hint_msg').append("<br/<br/>"+cg_comment_name_characters+"<br>");
            }

            else if(commentLength<3){

                var cg_comment_comment_characters = $('#cg_comment_comment_characters').val();
                $('#cg_slider_comment_hint_msg').append("<br/<br/>"+cg_comment_comment_characters+"<br>");
            }


            else if(allowSubmit==1){

                // Loading GIF





                $('#cg_slider_comment_hint_msg').empty();
                $('#cg_slider_comment_name').val("");
                $('#cg_slider_comment').val("");


                $('#cg_slider_top_hr_div').remove();

                // Loading GIF
                var loadingSource = $('#cg_loadingGifSource').val();
                $("#show_comments_slider").empty();
                $("#show_comments_slider").append("<img class='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                $("#cg_loading_gif_img").load(function(){$(this).toggle();});



                // Feld muss vorher geändert werden, da es innerhalb des AJAX Calls nicht gefunden werden kann
                countC = countC+1;
                //alert(countC);
                //alert(cg_picture_id);
                // Sehbares DIV mit Inhalt muss verändert werden
                $('.comments_cg_slider'+cg_picture_id).empty();
                $('.comments_cg_slider'+cg_picture_id).append(countC);
                // Unsichtbares Hidden Feld muss verändert werden zwecks Slider und Ansichten wechsel
                $("#countCommentsQuantity"+cg_picture_id).val(countC);

                $('#cg_i_am_not_a_robot .'+checkID+'').prop("checked",false);

                $.ajax({
                    url : post_cg_set_comment_slider_wordpress_ajax_script_function_name.cg_set_comment_slider_ajax_url,
                    type : 'post',
                    data : {
                        action : 'post_cg_set_comment_slider',
                        action1 : name,
                        action2 : comment,
                        action3 : checkID,
                        action4 : email,
                        action5 : timestamp,
                        action6 : cg_picture_id,
                        action7 : cg_galery_id,
                        action8 : cg_ThankYouComment
                    }
                }).done(function( response ) {

                    //   document.getElementById("show_comments_slider").innerHTML = response;
                    $("#show_comments_slider").html( response );

                });


                // Um zu vielen requests vorzubeugen
                var allowSubmit = 0;
                setTimeout(function(){ allowSubmit = 1; }, 5000);

            }
            else{
                alert("Wait 5 seconds till you can send again");
                $('#cg_slider_comment_hint_msg').empty();
                $('#cg_slider_comment_hint_msg').append("<br/<br/>Wait 5 seconds till you can send again<br>");
            }
        }

        else{
            $('#cg_slider_comment_hint_msg').empty();

            if(nameLength<2){
                var cg_comment_name_characters = $('#cg_comment_name_characters').val();
                $('#cg_slider_comment_hint_msg').append("<br/<br/>"+cg_comment_name_characters+"<br>");
            }

            if(commentLength<3){
                var cg_comment_comment_characters = $('#cg_comment_comment_characters').val();
                $('#cg_slider_comment_hint_msg').append("<br/<br/>"+cg_comment_comment_characters+"<br>");
            }

            var cg_comment_not_a_robot = $('#cg_comment_not_a_robot').val();
            $('#cg_slider_comment_hint_msg').append("<br/<br/>"+cg_comment_not_a_robot+"<br>");
        }

        //return false;

    })
})