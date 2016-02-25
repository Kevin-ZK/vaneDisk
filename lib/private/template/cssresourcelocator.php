<?php

namespace OC\Template;

class CSSResourceLocator extends ResourceLocator {
	/**
	 * @param string $style
	 */
	public function doFind($style) {
		if (strpos($style, '3rdparty') === 0
			&& $this->appendIfExist($this->thirdpartyroot, $style.'.css')
			|| $this->appendIfExist($this->serverroot, $style.'.css')
			|| $this->appendIfExist($this->serverroot, 'core/'.$style.'.css')
		) {
			return;
		}
		$app = substr($style, 0, strpos($style, '/'));
		$style = substr($style, strpos($style, '/')+1);
		$app_path = \OC_App::getAppPath($app);
		$app_url = \OC_App::getAppWebPath($app);
		$this->append($app_path, $style.'.css', $app_url);
	}

	/**
	 * @param string $style
	 */
	public function doFindTheme($style) {
		$theme_dir = 'themes/'.$this->theme.'/';
		$this->appendIfExist($this->serverroot, $theme_dir.'apps/'.$style.'.css')
			|| $this->appendIfExist($this->serverroot, $theme_dir.$style.'.css')
			|| $this->appendIfExist($this->serverroot, $theme_dir.'core/'.$style.'.css');
	}
}
