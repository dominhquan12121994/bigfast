<style>
    @media print {
        .body-html {
            width: <?php echo $dataSize['width']; ?>mm;
            height: <?php echo $dataSize['height']; ?>mm;
        }
    }
    body{
        background: white;
        margin: 0;
    }
    .body-html {
        width: calc(<?php echo $dataSize['width']; ?>*3.7795275591px);
        height: calc(<?php echo $dataSize['height']; ?>*3.7795275591px);
        overflow: hidden;
    }
</style>
<div class= "body-html">
    {!! $html !!}
</div>

<script>
    // window.print();
</script>