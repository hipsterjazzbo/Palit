<?php
// The function takes in an image resource (the result from one
// of the GD imagecreate... functions) as well as a width and
// height for the size of colour palette you wish to create.
// This defaults to a 3x3, 9 block palette.
function build_palette($img_resource, $palette_w = 3, $palette_h = 3) {
    $width = imagesx($img_resource);
    $height = imagesy($img_resource);

    // Calculate the width and height of each palette block
    // based upon the size of the input image and the number
    // of blocks.
    $block_w = round($width / $palette_w);
    $block_h = round($height / $palette_h);

    for($y = 0; $y < $palette_h; $y++) {
        for($x = 0; $x < $palette_w; $x++) {
            // Calculate where to take an image sample from the soruce image.
            $block_start_x = ($x * $block_w);
            $block_start_y = ($y * $block_h);

            // Create a blank 1x1 image into which we will copy
            // the image sample.
            $block = imagecreatetruecolor(1, 1);

            imagecopyresampled($block, $img_resource, 0, 0, $block_start_x, $block_start_y, 1, 1, $block_w, $block_h);

            // Convert the block to a palette image of just one colour.
            imagetruecolortopalette($block, true, 1);


            // Find the RGB value of the block's colour and save it
            // to an array.
            $colour_index = imagecolorat($block, 0, 0);
            $rgb = imagecolorsforindex($block, $colour_index);

            $colour_array[$x][$y]['r'] = $rgb['red'];
            $colour_array[$x][$y]['g'] = $rgb['green'];
            $colour_array[$x][$y]['b'] = $rgb['blue'];

            imagedestroy($block);
        }
    }

    imagedestroy($img_resource);
    return $colour_array;
}

function rgb2html($r, $g=-1, $b=-1)
{
    if (is_array($r) && sizeof($r) == 3)
        list($r, $g, $b) = $r;

    $r = intval($r); $g = intval($g);
    $b = intval($b);

    $r = dechex($r<0?0:($r>255?255:$r));
    $g = dechex($g<0?0:($g>255?255:$g));
    $b = dechex($b<0?0:($b>255?255:$b));

    $color = (strlen($r) < 2?'0':'').$r;
    $color .= (strlen($g) < 2?'0':'').$g;
    $color .= (strlen($b) < 2?'0':'').$b;
    return '#'.$color;
}

if($_POST)
{
	$img = imagecreatefromjpeg($_POST['image']);
	$palettes = build_palette($img, 7, 7);

	echo '<img src="' . $_POST['image'] . '" style="width: 300px; height: auto;"/>';

	echo '<table>';
	foreach($palettes as $palette)
	{
		echo '<tr>';

		foreach($palette as $rgb)
		{
			echo '<td style="background-color: ' . rgb2html($rgb['r'], $rgb['g'], $rgb['b']) . '; width: 25px; height: 25px;">&nbsp;</td>';
		}

		echo '</tr>';
	}
	echo '</table>';
}
?>

<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<input type="text" name="image"/>
	<input type="submit"/>
</form>