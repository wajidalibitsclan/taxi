<?php /** @noinspection ALL */

    namespace Themes\Findhouse\Agencies\Controllers;

    use App\Http\Controllers\Controller;
    use \Themes\Findhouse\User\Models\User;
    use DB;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Themes\Findhouse\Agencies\Models\Agencies;
    use Themes\Findhouse\Agencies\Models\AgenciesAgent;
    use Modules\Contact\Models\Contact;
    use Modules\FrontendController;
    use Themes\Findhouse\Property\Models\Property;
    use Modules\Review\Models\Review;
    use Modules\User\Events\NewVendorRegistered;
    use Modules\Vendor\Models\VendorRequest;

    class AgencyManagerController extends FrontendController
    {

        /**
         * @var Agencies
         */
        private $agencies;
        /**
         * @var AgenciesAgent
         */
        private $agenciesAgentClass;

        public function __construct()
        {
            parent::__construct();
            $this->agenciesClass = new Agencies();
            $this->agenciesAgentClass = new AgenciesAgent();
        }

        public function checkPermission($permission = false, $id = false)
        {
            if ($permission) {
                if (!Auth::id() or !Auth::user()->hasPermissionTo($permission)) {
                    abort(403);
                }
            }
            if (!is_agency_owner()) {
                abort(403);
            }
        }

        public function manageAgency(Request $request)
        {
            $user_id = Auth::id();
            $this->checkPermission('agencies_view');
            $rows = $this->agenciesClass::query()->select("bravo_agencies.*")->where("bravo_agencies.create_user", $user_id);
            if (!empty($search = $request->input("s"))) {
                $rows->where(function ($query) use ($search) {
                    $query->where('bravo_agencies.name', 'LIKE', '%'.$search.'%');
                    $query->orWhere('bravo_agencies.content', 'LIKE', '%'.$search.'%');
                });

                if (setting_item('site_enable_multi_lang') && setting_item('site_locale') != app_get_locale()) {
                    $rows->leftJoin('bravo_agencies_translations', function ($join) use ($search) {
                        $join->on('bravo_agencies.id', '=', 'bravo_agencies_translations.origin_id');
                    });
                    $rows->orWhere(function ($query) use ($search) {
                        $query->where('bravo_agencies_translations.name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('bravo_agencies_translations.content', 'LIKE', '%'.$search.'%');
                    });
                }
            }


            $data = [
                'rows'        => $rows->paginate(5),
                'breadcrumbs' => [
                    [
                        'name' => __('Manage Agency'),
                        'url'  => route('agency.vendor.index')
                    ],
                    [
                        'name'  => __('All'),
                        'class' => 'active'
                    ],
                ],
                'page_title'  => __("Manage Agency"),
            ];
            return view('Agencies::frontend.manageAgency.index', $data);
        }

        public function edit(Request $request, $id)
        {
            $this->checkPermission('agencies_view', $id);
            $user_id = Auth::id();
            $row = $this->agenciesClass::where("create_user", $user_id);
            $row = $row->find($id);
            if (empty($row)) {
                return redirect(route('agency.vendor.index'))->with('warning', __('Agency not found!'));
            }
            $translation = $row->translateOrOrigin($request->query('lang'));
            $data = [
                'translation' => $translation,
                'row'         => $row,
                'breadcrumbs' => [
                    [
                        'name' => __('Manage Agency'),
                        'url'  => route('agency.vendor.index')
                    ],
                    [
                        'name'  => __('Edit Agency :name', ['name' => $row->name]),
                        'class' => 'active'
                    ],
                ],
                'page_title'  => __("Edit Agency"),
            ];
            return view('Agencies::frontend.manageAgency.edit', $data);

        }

        public function store(Request $request, $id)
        {
            $this->checkPermission('agencies_create', $id);
            if ($id > 0) {
                $row = $this->agenciesClass::find($id);
                if (empty($row)) {
                    return redirect(route('agency.vendor.index'));
                }
                if ($row->create_user != Auth::id()) {
                    return redirect(route('agency.vendor.index'));
                }
            } else {
                $row = new $this->agenciesClass();
            }

            $agenciesId = $id > 0 ? $id : null;
            $rules = [
                'name'     => 'required|unique:bravo_agencies,name,'.$agenciesId,
                'content'  => 'required',
                'image_id' => 'required',
            ];
            if (!is_default_lang()) {
                unset($rules['image_id']);
            }
            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $data = $request->input();
            if (!empty($data['social'])) {
                $data['social'] = json_encode($data['social']);
            }
            $row->fill($data);
            $res = $row->saveOriginOrTranslation($request->input('lang'), true);

            if ($res) {
                return redirect()->back()->with('success', __('Agency updated'));
            } else {
                return redirect()->back()->with('errors', __('Agency fail'));
            }
        }


        public function listAgent(Request $request, $agency_id)
        {
            $user_id = Auth::id();
            $this->checkPermission('agencies_view', $agency_id);
            $current_agency = Agencies::findOrFail($agency_id);
            $data = [
                'current_agency' => $current_agency,
                'rows'           => $current_agency->agent()->paginate(5),
                'breadcrumbs'    => [
                    [
                        'name' => __('Manage Agent'),
                        'url'  => route('agency.vendor.index')
                    ],
                    [
                        'name' => __('Agency :name', ['name' => $current_agency->name]),
                        'url'  => route('agency.vendor.edit', ['id' => $agency_id])
                    ],
                    [
                        'name'  => __('All Agent'),
                        'class' => 'active'
                    ],
                ],
                'page_title'     => __("Manage Agent"),
            ];
            return view('Agencies::frontend.manageAgency.agent.list', $data);
        }
        public function storeAgent(Request $request, $agency_id)
        {
            $this->checkPermission('agencies_view', $agency_id);
            $current_agency = Agencies::findOrFail($agency_id);
            $rules = [
                'first_name'    => [
                    'required',
                    'string',
                    'max:255'
                ],
                'last_name'     => [
                    'required',
                    'string',
                    'max:255'
                ],
                'business_name' => [
                    'required',
                    'string',
                    'max:255'
                ],
                'email'         => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users'
                ],
                'password'      => [
                    'required',
                    'string'
                ],
            ];
            $messages = [
                'email.required'         => __('Email is required field'),
                'email.email'            => __('Email invalidate'),
                'password.required'      => __('Password is required field'),
                'first_name.required'    => __('The first name is required field'),
                'last_name.required'     => __('The last name is required field'),
                'business_name.required' => __('The business name is required field'),
            ];
            $validator = \Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput($request->all());
            } else {
                $user = new User();
                $user = $user->fill([
                    'first_name'    => $request->input('first_name'),
                    'last_name'     => $request->input('last_name'),
                    'email'         => $request->input('email'),
                    'password'      => Hash::make($request->input('password')),
                    'business_name' => $request->input('business_name'),
                    'phone'         => $request->input('phone'),
                ]);
                $user->status = 'publish';
                $user->save();
                if (empty($user)) {
                    return $this->sendError(__("Can not register"));
                }

                $current_agency->agent()->save($user);
                //                check vendor auto approved
                $vendorAutoApproved = setting_item('vendor_auto_approved');
                $dataVendor['role_request'] = setting_item('vendor_role');
                if ($vendorAutoApproved) {
                    if ($dataVendor['role_request']) {
                        $user->assignRole($dataVendor['role_request']);
                    }
                    $dataVendor['status'] = 'approved';
                    $dataVendor['approved_time'] = now();
                } else {
                    $dataVendor['status'] = 'pending';
                    $user->assignRole('customer');
                }
                $vendorRequestData = $user->vendorRequest()->save(new VendorRequest($dataVendor));
                try {
                    event(new NewVendorRegistered($user, $vendorRequestData));
                } catch (\Exception $exception) {
                    Log::warning("NewVendorRegistered: ".$exception->getMessage());
                }
                if ($vendorAutoApproved) {
                    return back()->with('success', __('Register success'));
                } else {
                    return back()->with('success', __("Register success. Please wait for admin approval"));
                }
            }
        }

        public function removeAgent(Request $request,$agency_id,$id)
        {
            $this->checkPermission('agencies_view', $agency_id);
            $current_agency = Agencies::findOrFail($agency_id);
            $user = User::findOrFail($id);
            $current_agency->agent()->detach($user);
            return back()->with('success', __('Success'));
        }



    }
