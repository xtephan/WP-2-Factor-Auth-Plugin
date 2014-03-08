<?php
/*
 * Admin view for the two factor auth plugin
 * @author Stefan Fodor (stefan@unserialized.dk)
 */
$that->loadCSS();
$that->loadJS();

//on post, update data
if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

    //enable or disable
    $that->update_option( 'is_enabled', ($_POST["enabled"] == 1 ? true : false ) );

    //do we need to generate new seed
    if( $_POST["new_key"] == "1" ) {
        $that->generateNewSecret();
    }
}
?>

<h2>2 Factor Authentication</h2>

<div id="setting-form">

    <h3>Settings</h3>

    <form action="" method="post">
        <?php
        $isEnabled = $that->get_option('is_enabled');
        ?>
        Enable 2 Factor Authentication: <br />
        <input type="radio" name="enabled" id="ey" value="1" <?php if( $isEnabled ) { echo 'checked'; } ?>>
            <label for="ey">Yes</label><br/>
        <input type="radio" name="enabled" id="en" value="0" <?php if( !$isEnabled ) { echo 'checked'; } ?>>
            <label for="en">No</label>

        <hr id="separator">

        Secret Key: <br />
        <input type="radio" name="new_key" id="se" value="0" checked>
            <label for="se">Use existing one</label><br/>
        <input type="radio" name="new_key" id="sn" value="1">
            <label for="sn">Generate new one</label>

         <br /><br />

        <input type="submit" value="Save" class="button action" />


    </form>
</div>

<div id="setting-secret">

    <h3>Secret Key</h3>

    <input type="button" id="show-secret-key" value="Show secret key" class="button action" />

    <div id="secret-key">
        <img src="<?php echo $that->getQRCode() ?>" />
        <p id="visible_key"><?php echo $that->get_option('secret_key') ?></p>
    </div>

</div>