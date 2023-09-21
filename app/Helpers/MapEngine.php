<?php
namespace App\Helpers;
class MapEngine
{
    protected static $is_generated = false;
    public static function scripts()
    {
        if(static::$is_generated){
           return;
        }
        $html = '';
        switch (setting_item('map_provider')) {
            case "gmap":
                $html .= sprintf("<script src='https://maps.googleapis.com/maps/api/js?key=%s&libraries=places'></script>", setting_item('map_gmap_key'));
                $html .= sprintf("<script src='https://unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js'></script>");
                $html .= sprintf("<script src='%s'></script>", url('libs/infobox.js'));
                break;
            case "osm":
                $html .= sprintf("<script src='%s'></script>", url('libs/leaflet1.4.0/leaflet.js'));
                $html .= sprintf("<link rel='stylesheet' href='%s'>", url('libs/leaflet1.4.0/leaflet.css'));
                break;
        }
        $html .= sprintf("<script src='%s'></script>", url('module/core/js/map-engine.js?_ver='.config('app.asset_version')));
        static::$is_generated = true;
        return $html;
    }
}
