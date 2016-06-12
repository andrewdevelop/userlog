<?php 

namespace Andrewdevelop\Userlog;

/**
* EventListener
*/
class EventListener
{
	
	protected $user_model;

	public function __construct()
	{
		$this->user_model = app('config')->get('auth.model');
	}

	public function onLogin($model)
	{



		app('Components\Customer\Contracts\Notifiable')
			->to($model)
			->from(null)
			->withType('message')
			->withSubject('notification.SUBJECT_USER_CREATED')
			->withBody('notification.BODY_USER_CREATED')
			->regarding($model)
			->dismissable(true)
			->send();
	}

	
	/**
	 * Register the listeners for the subscriber.
	 * @param  Illuminate\Events\Dispatcher $event
	 * @return void
	 */
	public function subscribe($event)
	{
		$class = get_class($this);
		$event->listen('auth.login', 		$class.'@onLogin');
		$event->listen('auth.logout',		$class.'@onLogout');

		/*	
		$event->listen('customer.updating',		$class.'@onUpdating');
		$event->listen('customer.creating', 	$class.'@onCreating');
		$event->listen('customer.saving',		$class.'@onSaving');
		$event->listen('customer.saved',		$class.'@onSaved');
		*/
	}
}