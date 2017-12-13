<?php //Add facebook pixel to track Facebook ads ?>
<?php if($this->pixel):

$track = $this->track ? $this->a('track') : ['PageView', 'ViewContent'];
 ?>

<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?= $this->ee($this->pixel, "js") ?>');
<?php foreach($track as $key => $val){
    if(is_numeric($key)) {
        echo "fbq('track', '$val');\n";
    } elseif($val) {
        $val = json_encode($val);
        echo "fbq('track', '$key', $val);\n";
    } else {
        echo "fbq('track', '$key');\n";
    }
}
?>
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= $this->ee($this->pixel, "js") ?>&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->

<?php endif; ?>
