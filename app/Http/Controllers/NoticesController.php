<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Notice;
use Auth;
use Illuminate\Http\Request;
use App\Provider;
use Illuminate\Contracts\Auth\Guard;

class NoticesController extends Controller {

	/**
	 * When add this, user can log in
	 *Create a new controller notice
	 */
	public function __construct()
	{
		$this->middleware('auth');

		parent::__construct();
	}

	public function index()
	{
		$notices = $this->user->notices;

		return view('notices.index', compact('notices'));
	}

	/**
	 * Show a page to create a new notice
	 *
	 *@return \Response
	 */
	public function create()
	{
		//get list of providers
		$providers = Provider::lists('name', 'id');

		return view('notices.create', compact('providers'));

		//load a view to create a new notice
		//return view('notices.create');
	}

	/**
	 *Ask the user to confirm the DMCA that will be delivered
	 *
	 *@param  PrepareNoticeRequest $request
	 *@return \Response
	 */
	public function confirm(Requests\PrepareNoticeRequest $request)
	{
		$template = $this->compileDmcaTemplate($data = $request->all());
		
		session()->flash('dmca', $data);

		return view('notices.confirm', compact('template'));
	}

	/**
	 *Store a new DMCA notice 
	 *
	 *@param  Request $request
	 *@return \Illuminate\Http\RedirectResponse\Illuminate\Routing\Redirector
	 */
	public function store(Request $request)
	{
		$notice = $this->createNotice($request);

		session()->flash('notification', 'You are now logged in');

		//Fire off the email
		\Mail::queue(['text'=> 'emails.dmca'], compact('notice'), function($message) use ($notice) {
			$message->from($notice->getOwnerEmail())
					->to($notice->getRecipientEmail())
					->subject('DMCA Notice');
		});

		flash('Your DMCA notice has been delivered');

		return redirect('notices');

		//return session()->get('dmca');
	}

	public function update($noticeId, Request $request)
	{
		$isRemoved = $request->has('content_removed');

		Notice::findOrFail($noticeId)
		->update(['content_removed' => $isRemoved]);

		return redirect()->back();
	}


	/**
	 *Compile the DMCA template form the form data 
	 *
	 *@param  $data
	 *@return mixed
	 */
	public function compileDmcaTemplate($data)
	{
		$data = $data + [
			'name' => $this->user->name,
			'email' => $this->user->email,
		];

		return view()->file(app_path('Http/Templates/dmca.blade.php'), $data);
	}

	/**
	 * Create and persist a new DMCA notice
	 *@param Request $request
	 */
	public function createNotice(Request $request)
	{
		//$data = session()->get('dmca');
		
		//$notice = Notice::open($data)->useTemplate($request->input('template'));

		$notice = session()->get('dmca') + ['template' => $request->input('template')];

		//Auth::user()->notices()->save($notice);
		$notice = $this->user->notices()->create($notice);

		return $notice;

	}

}
