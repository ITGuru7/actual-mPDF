<?php
	require_once __DIR__ . '/vendor/autoload.php';

	use Mpdf\Mpdf;

	$mpdf = new Mpdf([
		'mode' => 'c',
		'fontDir' => [ __DIR__ . '/ttfonts'],
    'fontdata' => [
			'Helvetica' => [
					'R' => 'HelveticaCERegular.ttf',
					'B' => 'HelveticaCEBold.ttf',
			],
			'BebasNeue' => array(
					'R' => 'BebasNeue.ttf',
			),
    ],
    'default_font' => 'Helvetica'
	]);

	$mpdf->showImageErrors = true;
	$mpdf->setBasePath('html');

	$html = file_get_contents('html/index.html');
	$html = str_replace('style-html.css', 'style-pdf.css', $html);

	$html = str_replace('<table class="competency-level">', '<table class="competency-level" style="width: 100%; padding-left: 5pt ;">', $html);
	$html = str_replace('<td>1</td>', '<td style="text-align: center;">1</td>', $html);
	$html = str_replace('<td>2</td>', '<td style="color: #7f7f83; text-align: center;">2</td>', $html);
	$html = str_replace('<td>4</td>', '<td style="text-align: center ; background: none;">4</td>', $html);

// comaprtaive image
	$html = str_replace('<img class="img-comaprtaive" src="images/comaprtaive.png" width="855" height="340">', '<img class="img-comaprtaive" src="images/comaprtaive.png" width="703" height="282">', $html);
	$coma = imagecreatefrompng("html/images/comaprtaive/bg.png");

//	imagecopyresampled(dst_image, src_image, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)

	$coma_score = imagecreatefrompng("html/images/comaprtaive/score-txt-bg.png");
	list($width, $height) = getimagesize("html/images/comaprtaive/score-txt-bg.png");
	imagecopyresampled($coma, $coma_score, 300, 250, 0, 0, $width, $height, $width, $height);

	$coma_norm = imagecreatefrompng("html/images/comaprtaive/norm-txt-bg.png");
	list($width, $height) = getimagesize("html/images/comaprtaive/norm-txt-bg.png");
	imagecopyresampled($coma, $coma_norm, 300, 390, 0, 0, $width, $height, $width, $height);

	$coma_percentile = imagecreatefrompng("html/images/comaprtaive/percentile-txt-bg.png");
	list($width, $height) = getimagesize("html/images/comaprtaive/percentile-txt-bg.png");
	imagecopyresampled($coma, $coma_percentile, 300, 530, 0, 0, $width, $height, $width, $height);

//	imagettftext(image, size, angle, x, y, color, fontfile, text)

//	fonts
	$helvetica = __DIR__ . '/ttfonts/HelveticaCERegular.ttf';
	$bebas = __DIR__ . '/ttfonts/BebasNeueBold.ttf';

	$size = 35;

	$color = imagecolorallocate($coma, 0x6c, 0x6d, 0x70);
	imagettftext($coma, $size, 0, 60, 315, $color, $helvetica, "Score");
	imagettftext($coma, $size, 0, 60, 450, $color, $helvetica, "Norm");
	imagettftext($coma, $size, 0, 60, 595, $color, $helvetica, "Percentile");

	$color = imagecolorallocate($coma, 0xff, 0xff, 0xff);
	imagettftext($coma, $size, 0, 340, 315, $color, $helvetica, "Your competency score");
	imagettftext($coma, $size, 0, 340, 450, $color, $helvetica, "Average score of others");
	imagettftext($coma, $size, 0, 340, 595, $color, $helvetica, "Your rank against others");

	$color = imagecolorallocate($coma, 0xae, 0x94, 0xad);
	$str = "4.9";
	$bbox = imagettfbbox($size, 0, $helvetica, $str);	//  $bbox[4] = text_width, -$bbox[5] = text_height
	imagettftext($coma, $size, 0, 2136-$bbox[4], 315, $color, $helvetica, $str);


	$color = imagecolorallocate($coma, 0x92, 0xaa, 0xa5);
	$str = "7.1";
	$bbox = imagettfbbox($size, 0, $helvetica, $str);	//  $bbox[4] = text_width, -$bbox[5] = text_height
	imagettftext($coma, $size, 0, 2136-$bbox[4], 450, $color, $helvetica, $str);

	$color = imagecolorallocate($coma, 0x00, 0xbf, 0xc9);
	$str = "30%";
	$bbox = imagettfbbox($size, 0, $helvetica, $str);	//  $bbox[4] = text_width, -$bbox[5] = text_height
	imagettftext($coma, $size, 0, 2136-$bbox[4], 595, $color, $helvetica, $str);

	$color = imagecolorallocate($coma, 0x6c, 0x6d, 0x70);
	for ($i = 0; $i <= 10; $i ++) {
		$str = $i;
		$bbox = imagettfbbox($size, 0, $helvetica, $str);	//  $bbox[4] = text_width, -$bbox[5] = text_height
		imagettftext($coma, $size, 0, 920+(107*$i)-$bbox[4]/2, 100, $color, $helvetica, $str);
	}
	for ($i = 0; $i <= 5; $i ++) {
		$str = $i*20;
		$bbox = imagettfbbox($size, 0, $helvetica, $str);	//  $bbox[4] = text_width, -$bbox[5] = text_height
		imagettftext($coma, $size, 0, 920+(215*$i)-$bbox[4]/2, 800, $color, $helvetica, $str);
	}

//	bool imagedashedline ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $color )
	$color = imagecolorallocate($coma, 0xa5, 0xa7, 0xaa);
	imagesetthickness($coma, 3);
	for ($i = 0; $i <= 10; $i ++) {
		imagedashedline($coma, 920+(107*$i), 150, 920+(107*$i), 720, $color);
	}

	$coma_score_val = imagecreatefrompng("html/images/comaprtaive/score-4.9.png");
	list($width, $height) = getimagesize("html/images/comaprtaive/score-4.9.png");
	imagecopyresampled($coma, $coma_score_val, 920, 250, 0, 0, $width, $height, $width, $height);

	$coma_norm_val = imagecreatefrompng("html/images/comaprtaive/norm-7.1.png");
	list($width, $height) = getimagesize("html/images/comaprtaive/norm-7.1.png");
	imagecopyresampled($coma, $coma_norm_val, 920, 390, 0, 0, $width, $height, $width, $height);

	$coma_percentile_val = imagecreatefrompng("html/images/comaprtaive/percentile-30.png");
	list($width, $height) = getimagesize("html/images/comaprtaive/percentile-30.png");
	imagecopyresampled($coma, $coma_percentile_val, 920, 530, 0, 0, $width, $height, $width, $height);

	imagepng($coma, "html/images/comaprtaive.png");

//	overall
	$html = str_replace('<img class="img-overall" src="images/overall.png" width="850" height="230">', '<img class="img-comaprtaive" src="images/overall.png" width="703" height="282">', $html);
	$overall = imagecreatefrompng("html/images/overall/bg.png");

//	imagecopyresampled(dst_image, src_image, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)

	$score_bg = imagecreatefrompng("html/images/overall/bg-gf.png");
	list($width, $height) = getimagesize("html/images/overall/bg-gf.png");
	imagecopyresampled($overall, $score_bg, 135, 88, 0, 0, $width, $height, $width, $height);

	$size = 50;
	$color = imagecolorallocate($coma, 0xff, 0xff, 0xff);
	$str = "7.8";
	imagettftext($overall, $size, 0, 227, 436, $color, $bebas, $str);

	$size = 40;
	$color = imagecolorallocate($coma, 0x58, 0x59, 0x5a);

	$str = "OVERALL";
	imagettftext($overall, $size, 0, 190, 190, $color, $bebas, $str);

	$str = "SCORE";
	imagettftext($overall, $size, 0, 210, 240, $color, $bebas, $str);

	$line = imagecreatefrompng("html/images/overall/line.png");
	list($width, $height) = getimagesize("html/images/overall/line.png");
	imagecopyresampled($overall, $line, 510, 150, 0, 0, $width, $height, $width, $height);

	$line = imagecreatefrompng("html/images/overall/f.png");
	list($width, $height) = getimagesize("html/images/overall/f.png");
	imagecopyresampled($overall, $line, 555, 140, 0, 0, $width, $height, $width, $height);
	imagecopyresampled($overall, $line, 865, 240, 0, 0, $width, $height, $width, $height);
	imagecopyresampled($overall, $line, 1175, 140, 0, 0, $width, $height, $width, $height);
	imagecopyresampled($overall, $line, 1800, 140, 0, 0, $width, $height, $width, $height);

	$line = imagecreatefrompng("html/images/overall/gf.png");
	list($width, $height) = getimagesize("html/images/overall/gf.png");
	imagecopyresampled($overall, $line, 1490, 240, 0, 0, $width, $height, $width, $height);

	imagettftext($overall, $size, 0, 610, 510, $color, $bebas, "NOT FIT");
	imagettftext($overall, $size, 0, 910, 130, $color, $bebas, "POOR FIT");
	imagettftext($overall, $size, 0, 1180, 510, $color, $bebas, "MODERATE FIT");
	imagettftext($overall, $size, 0, 1535, 130, $color, $bebas, "GOOD FIT");
	imagettftext($overall, $size, 0, 1820, 510, $color, $bebas, "OPTIMAL FIT");

	imagettftext($overall, $size, 0, 650, 260, $color, $bebas, "NF");
	imagettftext($overall, $size, 0, 955, 360, $color, $bebas, "PF");
	imagettftext($overall, $size, 0, 1260, 260, $color, $bebas, "MF");
	imagettftext($overall, $size, 0, 1580, 360, $color, $bebas, "GF");
	imagettftext($overall, $size, 0, 1890, 260, $color, $bebas, "OF");

	imagepng($overall, "html/images/overall.png");

//	fit
	$html = str_replace('<img src="images/fit.png"  width="850" height="180">', '<img src="images/fit.png"  width="700" height="145">', $html);
	$fit = imagecreatetruecolor(2185, 456);
	$black = imagecolorallocate($fit, 0, 0, 0);
	// Make the background transparent
	imagecolortransparent($fit, $black);

//	imagecopyresampled(dst_image, src_image, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)

	$fit_bg = imagecreatefrompng("html/images/fit/bg.png");
	list($width, $height) = getimagesize("html/images/fit/bg.png");
	for ($i = 0; $i < 5; $i ++) {
		imagecopyresampled($fit, $fit_bg, 90+$i*425, 0, 0, 0, $width, $height, $width, $height);
	}
	$fit_bg_A = imagecreatefrompng("html/images/fit/bg-A.png");
	list($width, $height) = getimagesize("html/images/fit/bg-A.png");
	imagecopyresampled($fit, $fit_bg_A, 515, 0, 0, 0, $width, $height, $width, $height);

	$line = imagecreatefrompng("html/images/fit/line.png");
	list($width, $height) = getimagesize("html/images/fit/line.png");
	for ($i = 0; $i < 5; $i ++) {
		imagecopyresampled($fit, $line, 245+$i*425, 325, 0, 0, $width, $height, $width, $height);
	}

	$c_5 = ['n', 'p', 'm', 'g', 'o'];
	for ($i = 0; $i < 5; $i ++) {
		$c = $c_5[$i];
		$img_src = "html/images/fit/c-".$c.".png";
		$fit_c = imagecreatefrompng($img_src);
		list($width, $height) = getimagesize($img_src);
		imagecopyresampled($fit, $fit_c, 220+425*$i, 390, 0, 0, $width, $height, $width, $height);

		if($c == 'p') {
			$img_src = "html/images/fit/mark.png";
			$fit_c = imagecreatefrompng($img_src);
			list($width, $height) = getimagesize($img_src);
			imagecopyresampled($fit, $fit_c, 220+425*$i+11, 390+15, 0, 0, $width, $height, $width, $height);
		}
	}

	$size = 30;
	$color = imagecolorallocate($fit, 0x58, 0x59, 0x5a);
	$fit_str = ['NOT FIT', 'POOR FIT', 'MODERATE FIT', 'GOOD FIT', 'OPTIMAL FIT'];
	for ($i = 0; $i < 5; $i ++) {
		$str = $fit_str[$i];
		$bbox = imagettfbbox($size, 0, $bebas, $str);	//  $bbox[4] = text_width, -$bbox[5] = text_height
		imagettftext($fit, $size, 0, 245+(423*$i)-$bbox[4]/2, 176, $color, $bebas, $str);
	}

	imagepng($fit, "html/images/fit.png");

	$mpdf->WriteHTML($html);

	$mpdf->Output();
?>
