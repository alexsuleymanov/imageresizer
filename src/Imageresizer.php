<?php
namespace ASweb\Imageresizer;

class Imageresizer
{
	public $src;
	public $dst;
	public $dsts;
	public $dstimgw;
	public $dstimgh;

	public $dstw;
	public $dsth;
	public $srcw;
	public $srch;

	public $dstx;
	public $dsty;
	public $srcx;
	public $srcy;

	public $type;
	public $outtype;
	public $jpegqual;
	public $bgcolor;

	public function __construct()
	{
		$this->type = 'jpg';
		$this->outtype = 'jpg';
		$this->jpegqual = 80;

		$this->dstimgw = $this->dstimgh = 'auto';
		$this->srcw = $this->srch = 0;
		$this->dstx = $this->dsty = $this->srcx = $this->srcy = 0;
	}

	public function resize()
	{
		switch ($this->type) {
			case 'jpg':
				$srcim = imagecreatefromjpeg($this->src);
				break;
			case 'gif':
				$srcim = imagecreatefromgif($this->src);
				break;
			case 'png':
				$srcim = imagecreatefrompng($this->src);
				break;
			default:
				$srcim = imagecreatefromjpeg($this->src);
				break;
		}

		$srcw = ($this->srcw) ? $this->srcw : imagesx($srcim);
		$srch = ($this->srch) ? $this->srch : imagesy($srcim);
		
		$this->dstw = ($this->dstw) ? $this->dstw : $this->dstimgw;
		$this->dsth = ($this->dsth) ? $this->dsth : $this->dstimgh;

		if ($this->dstimgh === 'auto' && $this->dstimgw > 0 && $srcw > 0)
			$this->dstimgh = round($srch * $this->dstimgw / $srcw);
		
		if ($this->dstimgw === 'auto' && $this->dstimgh > 0 && $srch > 0)
			$this->dstimgw = round($srcw * $this->dstimgh / $srch);

		$dstim = imagecreatetruecolor($this->dstimgw, $this->dstimgh);

		if ($this->type == 'png') {
			$bg = imagecolorallocate($dstim, 0, 0, 0);
			imageColorTransparent($dstim, $bg);
		}else {
			$white = imagecolorallocate($dstim, 255, 255, 255);
			imagefill($dstim, 0, 0, $white);
		}

		imagecopyresampled($dstim, $srcim, $this->dstx, $this->dsty, $this->srcx, $this->srcy, $this->dstw, $this->dsth, $srcw, $srch);

		$this->dst = $this->dst ? $this->dst : null;

		switch ($this->outtype) {
			case 'webp':
				imagewebp($dstim, $this->dst, 80);
				break;
			case 'jpg':
				if ($this->optimized == 1) {
					imagewebp($dstim, $this->dst, 80);
				} else {
					imagejpeg($dstim, $this->dst, $this->jpegqual);
				}
				break;
			case 'gif':
				imagegif($dstim, $this->dst);
				break;
			case 'png':
				imagepng($dstim, $this->dst);
				break;
			default:
				if ($this->optimized == 1) {
					imagewebp($dstim, $this->dst, 80);
				} else {
					imagejpeg($dstim, $this->dst, $this->jpegqual);
				}
			break;
		}
	}
}