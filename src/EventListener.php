<?php 



namespace Andrewdevelop\Userlog;

use Andrewdevelop\Userlog\Info;
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

		ini_set("display_errors", "1");
error_reporting(E_ALL);
	}

	public function onLogin($model)
	{

		$pk = $model->getKeyName();
		$misc = new Info;
		$msg = 'User by '.strtoupper($pk).' '.$model->getAttribute($pk).' logged in'.' ('.$misc->__toString().')';
		$this->log($msg);

		return true;
	}

	public function onRegister($model)
	{
		$pk = $model->getKeyName();
		$misc = new Info;
		$this->log('New user registered '.strtoupper($pk).' '.$model->getAttribute($pk).' '.' ('.$misc.')');
		return true;
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

				$misc = new Info;
				$this->log('User by '.strtoupper($pk).' '.$model->{$pk}.' update '.$attribute.' from "'.$original.'" to "'.$value.'" ('.$misc.')');
			}
		}

		return true;
	}


	protected function log($msg)
	{
		$message = $msg;
		$path = app('config')->get('userlog.log_path').DIRECTORY_SEPARATOR.'user_log_'.Carbon::now()->format('y_m').'.log';
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