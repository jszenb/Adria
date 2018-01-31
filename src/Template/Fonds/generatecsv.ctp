<html>
<head>
    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>
</head>
<body>
<center>
<?php
// app/Views/Subscribers/export.ctp
foreach ($fonds as $row):
	/*foreach ($row as $cell):
		// Escape double quotation marks
		$cell = '"' . preg_replace('/"/','""',$cell) . '"';
	endforeach;*/
	//echo implode(',', $row) . "<br>";
	//$tabrow=(array)$row;
	//$hello =print_r( $tabrow);

endforeach;
	echo "hello" . "<br><br>";
?>
<br/>
<a href="javascript:close();">Fermer</a>
<br/>
</center>
</body>
</html>
