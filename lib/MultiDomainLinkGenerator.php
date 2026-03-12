<?php
namespace MultiDomainLinkGenerator;

use App\RouterFactory;
use Nette\Application\IPresenterFactory;
use Nette\Application\IRouter;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Nette\Http\Url;

class MultiDomainLinkGenerator {

	/** @var IRouter */
	private \Nette\Application\Routers\RouteList $router;

	/** @var Url */
	private $defaultUrl;
	/** @var string */
	private $defaultScheme;

	public function __construct($defaultScheme = 'https', $defaultHost = null)
	{

		$this->router = RouterFactory::createRouter();
		//$this->router = $routerFactory->createRouter(RouterFactory::TYPE_WEB_ROUTER);
		$this->defaultScheme = $defaultScheme;
		$this->defaultUrl = $this->createUrlFromString($defaultHost);
	}

	/**
     * @param $dest
     * @param null|string|\Nette\Http\Url $refUrl
     * @return string
     * @throws InvalidLinkException|\InvalidArgumentException
     */
    public function link($dest, array $params = [], $refUrl = null){
		if($refUrl === null){
			$refUrl = $this->defaultUrl;
		}
		elseif(is_string($refUrl)){
			$refUrl = $this->createUrlFromString($refUrl);
		}

		if(!$refUrl instanceof Url){
			throw new \InvalidArgumentException('Invalid refUrl!');
		}

		$linkGenerator = new LinkGenerator($this->router, $refUrl);
		return $this->generateLink($linkGenerator, $dest, $params);
	}

	/**
	 * @return Url
	 */
	public function getDefaultUrl(){
		return $this->defaultUrl;
	}

	/**
     * @param $url
     */
    public function createUrlFromString($url): \Nette\Http\Url{
		if(!str_ends_with((string) $url, '/')){
			$url .= "/";
		}

		if(!str_starts_with((string) $url, 'http')){
			$url = $this->defaultScheme."://".$url;
		}
		return new Url($url);
	}

	private function generateLink(LinkGenerator $linkGenerator, $destination, array $params): ?string{
		//Moznost pridat za url #
		if (($pos = strrpos((string) $destination, '#')) !== FALSE) {
			$fragment = substr((string) $destination, $pos);
			$destination = substr((string) $destination, 0, $pos);
		} else {
			$fragment = '';
		}

		//linkGenerator generuje pouze absolutni URL a spadne pokud destination obsahuje na zacatku //
		if (str_starts_with((string) $destination, '//')) {
			$destination = substr((string) $destination, 2);
		}

		if (str_starts_with((string) $destination, ':')) {
			$destination = substr((string) $destination, 1);
		}

		$url = $linkGenerator->link($destination, $params);

		if ($fragment !== '' && $fragment !== '0') {
			$url .= $fragment;
		}

		return $url;
	}

}