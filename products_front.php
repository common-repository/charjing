<?php
$charjing_info=  get_post_meta($post->ID,"charjing_info",true);
$settings=get_option("charjing");
$checkout_url=get_permalink($settings["checkout"])."?product_id=".$post->ID;
if(@$settings["https"]=="1")
{
    $checkout_url=  str_replace("http://", "https://", $checkout_url);
}
?>
<hr>
<div>
    <strong style=" font-size: 20px;">Setup Amount:</strong>&nbsp;&nbsp;<font style=" font-size: 20px;">$ <?php echo $charjing_info["setup_price"]; ?></font><br><br>
    <strong style=" font-size: 20px;">Recurring:</strong>&nbsp;&nbsp;<font style=" font-size: 20px;"><?php echo $charjing_info["period_length"]; ?>&nbsp;<?php echo $charjing_info["period_option"]; ?></font><br><br>
</div>
 

<div class="buy_btn">
   <a href="<?php echo($checkout_url); ?>"> 
       <img src="<?php echo(plugins_url("/images/buy.jpg",__FILE__)); ?>" width="70" height="50">
   </a>
</div>
