var $j = jQuery.noConflict();

$j(function(){

    /*
    * When pressing the button, show the qr and the key
     */
    $j("#show-secret-key").click(function(){
        $j(this).hide();
        $j("#secret-key").show();
    });

});