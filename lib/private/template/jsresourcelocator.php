<?php

namespace OC\Template;

class JSResourceLocator extends ResourceLocator {
	/**
	 * @param string $script
	 */
	public function doFind($script) {
		$theme_dir = 'themes/'.$this->theme.'/';
		if (strpos($script, '3rdparty') === 0
			&& $this->appendIfExist($this->thirdpartyroot, $script.'.js')
			|| $this->appendIfExist($this->serverroot, $theme_dir.'apps/'.$script.'.js')
			|| $this->appendIfExist($this->serverroot, $theme_dir.$script.'.js')
			|| $this->appendIfExist($this->serverroot, $script.'.js')
			|| $this->appendIfExist($this->serverroot, $theme_dir.'core/'.$script.'.js')
			|| $this->appendIfExist($this->serverroot, 'core/'.$script.'.js')
		) {
			return;
		}
		$app = substr($script, 0, strpos($script, '/'));
		$script = substr($script, strpos($script, '/')+1);
		$app_path = \OC_App::getAppPath($app);
		$app_url = \OC_App::getAppWebPath($app);

		// missing translations files fill be ignored
		if (strpos($script, 'l10n/') === 0) {
			$this->appendIfExist($app_path, $script . '.js', $app_url);
			return;
		}
		$this->append($app_path, $script . '.js', $app_url);
	}

	/**
	 * @param string $script
	 */
	public function doFindTheme($script) {
	}
}
