<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(@$_POST["cmdSave"])
{
    update_option("charjing", $_POST["charjing"]);
}
$settings=get_option("charjing");
?>
<div class="wrap">
    <div id="icon-edit" class="icon32"><br /></div>
    <h2>Authorize.Net Account Settings</h2>
    <form action="" method="post">
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <fieldset class="options">
            <table class="widefat post fixed" cellspacing="1" width="500" >
                <tr>
                    <td width="200">
                        API Login ID:
                    </td>
                    <td>
                        <input type="text" name="charjing[loginid]" class="widefat" value="<?php echo($settings["loginid"]); ?>">
                    </td>
                </tr>
                <tr class="alternate">
                    <td width="200">
                        Transaction Key:
                    </td>
                    <td>
                        <input type="text" name="charjing[txn_key]" class="widefat" value="<?php echo($settings["txn_key"]); ?>">
                    </td>
                </tr>
                <tr>
                    <td width="200">
                        MD5-Hash Value:
                    </td>
                    <td>
                        <input type="text" name="charjing[md5hash]" class="widefat" value="<?php echo($settings["md5hash"]); ?>">
                    </td>
                </tr>
                <tr class="alternate">
                    <td width="200">
                        User Https:
                    </td>
                    <td>
                        <input type="checkbox" name="charjing[https]" value="1" <?php if(@$settings["https"]=="1")echo("checked"); ?>>
                    </td>
                </tr>
                <tr>
                    <td width="200">
                        Sandbox:
                    </td>
                    <td>
                        <input type="checkbox" name="charjing[sandbox]" value="1" <?php if(@$settings["sandbox"]=="1")echo("checked"); ?>>
                    </td>
                </tr>
                <tr class="alternate">
                    <td width="200">
                        Checkout Page:
                    </td>
                    <td>
                        <?php wp_dropdown_pages(array('selected' =>$settings["checkout"],'name' => 'charjing[checkout]', 'show_option_none' => 'Please select checkout page', 'option_none_value' => "" )); ?>
                    </td>
                </tr>
                <tr>
                    <td width="200">
                        Thank You Page:
                    </td>
                    <td>
                        <?php wp_dropdown_pages(array('selected' =>$settings["thanks"],'name' => 'charjing[thanks]', 'show_option_none' => 'Please select Thank You page', 'option_none_value' => "" )); ?>
                    </td>
                </tr>
                
                <tr class="alternate">
                    <td width="200">
                       
                    </td>
                    <td>
                        <input type="submit" name="cmdSave" value="Save">
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    </form>
</div>