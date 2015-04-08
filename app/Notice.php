<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;

class Notice extends Model {

	/**
	 * Fillable fields for a new notice
	 *
	 *@var array
	 */
	protected $fillable = [
		'infringing_title',
		'infringing_link',
		'original_link',
		'original_description',
		'template',
		'content_removed',
		'provider_id',
	];



	/**
	 * Open a new notice
	 *
	 *@param array $attributes
	 *@return static
	 */
	//public static function open(array $attributes)
	//{
		//return new static($attributes); 
	//}


	/**
	 * Set the email template for the notice
	 *
	 *@param string $template
	 */
	//public function useTemplate($template)
	//{
		//$this->template = $template;

		//return $this;
	//}


	/**
	 * A notice belongs to a recipient/Provider
	 *
	 *@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function recipient()
	{
		return $this->belongsTo('App\Provider', 'provider_id');
	}

	/**
	 * A notice is created by a user
	 *
	 *@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}

	/**
	 * Get the email address for the recipient of DMCA notice
	 *
	 *@return string
	 */
	public function getRecipientEmail()
	{
		return $this->recipient->copyright_email;
	}

	/**
	 * Get the email address of the owner of the notice
	 *
	 *@return string
	 */
	public function getOwnerEmail()
	{
		return $this->user->email;
	}

}
