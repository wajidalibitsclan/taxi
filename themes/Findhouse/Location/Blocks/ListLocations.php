<?php
namespace Themes\Findhouse\Location\Blocks;

use Modules\Template\Blocks\BaseBlock;
use Modules\Location\Models\Location;

class ListLocations extends BaseBlock
{
    function getOptions()
    {
        $list_service = [];
        foreach (get_bookable_services() as $key => $service) {
            $list_service[] = ['value'   => $key,
                               'name' => ucwords($key)
            ];
        }
        return ([
            'settings' => [
                [
                    'id'        => 'title',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Title')
                ],
                [
                    'id'        => 'desc',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Desc')
                ],
                [
                    'id'        => 'number',
                    'type'      => 'input',
                    'inputType' => 'number',
                    'label'     => __('Number Item')
                ],
                [
                    'id'            => 'order',
                    'type'          => 'radios',
                    'label'         => __('Order'),
                    'values'        => [
                        [
                            'value'   => 'id',
                            'name' => __("Date Create")
                        ],
                        [
                            'value'   => 'name',
                            'name' => __("Title")
                        ],
                    ],
                ],
                [
                    'id'            => 'order_by',
                    'type'          => 'radios',
                    'label'         => __('Order By'),
                    'values'        => [
                        [
                            'value'   => 'asc',
                            'name' => __("ASC")
                        ],
                        [
                            'value'   => 'desc',
                            'name' => __("DESC")
                        ],
                    ],
                ],
                [
                    'id'           => 'custom_ids',
                    'type'         => 'select2',
                    'label'        => __('List Location by IDs'),
                    'select2'      => [
                        'ajax'     => [
                            'url'      => route('location.admin.getForSelect2'),
                            'dataType' => 'json'
                        ],
                        'width'    => '100%',
                        'multiple' => "true",
                    ],
                    'pre_selected' => route('location.admin.getForSelect2', [
                        'pre_selected' => 1
                    ])
                ],
                [
                    'type'=> "checkbox",
                    'label'=>__("Link to location detail page?"),
                    'id'=> "to_location_detail",
                    'default'=>false
                ],
                [
                    'id'            => 'style',
                    'type'          => 'radios',
                    'label'         => __('Style'),
                    'values'        => [
                        [
                            'value'   => 'style_1',
                            'name' => __("Style 1")
                        ],
                        [
                            'value'   => 'style_2',
                            'name' => __("Style 2")
                        ],[
                            'value'   => 'style_3',
                            'name' => __("Style 3")
                        ],[
                            'value'   => 'style_4',
                            'name' => __("Style 4")
                        ],[
                            'value'   => 'style_5',
                            'name' => __("Style 5")
                        ],
                    ]
                ]
            ]
        ]);
    }

    public function getName()
    {
        return __('List Locations');
    }

    public function content($model = [])
    {
        if(empty($model['order'])) $model['order'] = "id";
        if(empty($model['style'])) $model['style'] = "style_1";
        if(empty($model['order_by'])) $model['order_by'] = "desc";
        if(empty($model['number'])) $model['number'] = 4;
        $model_location = Location::query()->with(['translations']);
        $model_location->where("status","publish");
        if(!empty( $model['custom_ids'] )){
            $model_location->whereIn("id",$model['custom_ids']);
        }
        $model_location->orderBy($model['order'], $model['order_by']);
        $list = $model_location->limit($model['number'])->get();
        $data = [
            'rows'         => $list,
            'title'        => $model['title'],
            'desc'         => $model['desc'] ?? "",
            'layout'       => !empty($model['style']) ? $model['style'] : "style_1",
            'to_location_detail'=>$model['to_location_detail'] ?? ''
        ];
        $template = (in_array($model['style'],array('style_1','style_2'))) ? 'index' : $model['style'];
        return view('Location::frontend.blocks.list-locations.'.$template, $data);
    }
}
