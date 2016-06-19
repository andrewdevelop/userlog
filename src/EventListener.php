<?php 

namespace Andrewdevelop\Userlog;

use Illuminate\Log\Writer;
use Carbon\Carbon;

/**
* EventListener
*/
class EventListener
{
	protected $logger;

	public function __construct(Writer $logger)
	{
		$this->logger = $logger;
	}

	public function onLogin($model)
	{
		$pk = $model->getKeyName();
		$this->log('User by '.strtoupper($pk).' '.$model->{$pk}.' logged in'.' ('.implode('; ', $this->getInfo()).')');
	}

	public function onRegister($model)
	{
		# code...
	}

	public function onUpdating($model)
	{
		if (count($model->getDirty())) {
			$pk = $model->getKeyName();
			
			foreach($model->getDirty() as $attribute => $value) {
				$original = $model->getOriginal($attribute);

				if (in_array($attribute, ['password'])) {
					$original = \Crypt::encrypt($original);
					$value = \Crypt::encrypt($value);
				}

				$this->log('User by '.strtoupper($pk).' '.$model->{$pk}.' update '.$attribute.' from "'.$original.'" to "'.$value.'" ('.implode('; ', $this->getInfo()).')');
			}
		}
	}

	protected function getInfo()
	{
		$info = [
			'ip' => app('request')->ip(),
			'ua' => app('request')->server('HTTP_USER_AGENT'),
			'url' => app('request')->fullUrl(),
			'http_referer' => app('request')->server('HTTP_REFERER'),
		];
		
		return $info;
	}

	protected function log($msg)
	{
		$message = $msg;
		$path = app('config')->get('userlog.log_path');
		$this->logger->useFiles($path);
		$this->logger->info($message);
	}
	
	/**
	 * Register the listeners for the subscriber.
	 * @param  Illuminate\Events\Dispatcher $event
	 * @return void
	 */
	public function subscribe($event)
	{
		$class = get_class($this);
		$event->listen('auth.login', 					$class.'@onLogin');
		$event->listen('eloquent.created: App\User', 	$class.'@onRegister');
		$event->listen('eloquent.updating: App\User', 	$class.'@onUpdating');

		# for customer component
		if (class_exists('Components\Customer\Models\User')) {
			$event->listen('eloquent.created: Components\Customer\Models\User', 	$class.'@onRegister');
			$event->listen('eloquent.updating: Components\Customer\Models\User', 	$class.'@onUpdating');
		}
	}
}