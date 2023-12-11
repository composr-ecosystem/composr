<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    world_regions
 */

/**
 * Get a map betweeen regions and possible counties.
 *
 * @return array Map: region=>list of counties
 */
function get_region_structure_GB()
{
    return array(
        'Scotland' => array('Inverclyde' => true, 'Renfrewshire' => true, 'West Dunbartonshire' => true, 'East Dunbartonshire' => true, 'Glasgow City' => true, 'East Renfrewshire' => true, 'North Lanarkshire' => true, 'Falkirk' => true, 'West Lothian' => true, 'City of Edinburgh' => true, 'Midlothian' => true, 'East Lothian' => true, 'Clackmannanshire' => true, 'Fife' => true, 'Dundee City' => true, 'Angus' => true, 'Aberdeenshire' => true, 'Aberdeen City' => true, 'Moray' => true, 'Highland and Isle of Skye' => true, 'Western Isles' => true, 'Argyll and Bute' => true, 'Perth and Kinross' => true, 'Stirling' => true, 'North Ayrshire' => true, 'East Ayrshire' => true, 'South Ayrshire' => true, 'Dumfries and Galloway' => true, 'South Lanarkshire' => true, 'Scottish Borders' => true, 'Orkney Islands' => true, 'Shetland Islands' => true,), // https://en.wikipedia.org/wiki/Council_Areas_of_Scotland=https://en.wikipedia.org/wiki/Subdivisions_of_Scotland [NOT https://en.wikipedia.org/wiki/Lieutenancy_areas_of_Scotland OR https://en.wikipedia.org/wiki/Registration_county OR https://en.wikipedia.org/wiki/Large_burghs OR https://en.wikipedia.org/wiki/Regions_and_districts_of_Scotland OR https://en.wikipedia.org/wiki/Counties_of_Scotland]
        'Northern Ireland' => array('County Antrim' => true, 'County Armagh' => true, 'County Down' => true, 'County Fermanagh' => true, 'County Londonderry' => true, 'County Tyrone' => true),
        'Wales' => array('Merthyr Tydfil' => true, 'Caerphilly' => true, 'Blaenau Gwent' => true, 'Torfae' => true, 'Monmouthshire' => true, 'Newport' => true, 'Cardiff' => true, 'Vale of Glamorgan' => true, 'Bridgend' => true, 'Rhondda Cynon Taff' => true, 'Neath Port Talbot' => true, 'Swansea' => true, 'Carmarthenshire' => true, 'Ceredigion' => true, 'Powys' => true, 'Wrexham' => true, 'Flintshire' => true, 'Denbighshire' => true, 'Conwy' => true, 'Gwynedd' => true, 'Isle of Anglesey' => true, 'Pembrokeshire' => true), // https://en.wikipedia.org/wiki/Administrative_divisions_of_Wales [NOT https://en.wikipedia.org/wiki/Preserved_counties_of_Wales OR https://en.wikipedia.org/wiki/Historic_counties_of_Wales]

        // https://en.wikipedia.org/wiki/Metropolitan_and_non-metropolitan_counties_of_England
        // except Herefordshire+Worcestershire are combined
        // and the following towns weren't promoted to counties: Blackpool, Blackburn with Darwen, Nottingham, Derby, Halton, Warrington, Telford and Wrekin, Leicester, Southend-on-Sea, Thurrock, Luton, Swindon, Medway, Torbay, Plymouth
        // [NOT https://en.wikipedia.org/wiki/Ceremonial_counties_of_England OR https://en.wikipedia.org/wiki/County_borough]
        'North East' => array('Darlington' => true, 'Middlesbrough' => true, 'Hartlepool' => true, 'Stockton-on-Tees' => true, 'Redcar and Cleveland' => true, 'Northumberland' => true, 'Tyne and Wear' => true, 'County Durham' => true),
        'North West' => array('Cheshire' => true, 'Cumbria' => true, 'Greater Manchester' => true, 'Lancashire' => true, 'Merseyside' => true),
        'Yorkshire and the Humber' => array('York' => true, 'Kingston upon Hull' => true, 'South Yorkshire' => true, 'West Yorkshire' => true, 'North Yorkshire' => true, 'East Riding of Yorkshire' => true),
        'East Midlands' => array('North Lincolnshire' => true, 'North East Lincolnshire' => true, 'Derbyshire' => true, 'Nottinghamshire' => true, 'Lincolnshire' => true, 'Leicestershire' => true, 'Rutland' => true, 'Northamptonshire' => true),
        'West Midlands' => array('Hereford and Worcester' => true, 'Shropshire' => true, 'Staffordshire' => true, 'Warwickshire' => true, 'West Midlands county' => true),
        'East' => array('Bedfordshire' => true, 'Cambridgeshire' => true, 'Essex' => true, 'Hertfordshire' => true, 'Norfolk' => true, 'Peterborough' => true, 'Suffolk' => true),
        'South East' => array('Brighton and Hove' => true, 'Portsmouth' => true, 'Southampton' => true, 'Milton Keynes' => true, 'Berkshire' => true, 'Buckinghamshire' => true, 'East Sussex' => true, 'Hampshire' => true, 'Isle of Wight' => true, 'Kent' => true, 'Oxfordshire' => true, 'Surrey' => true, 'West Sussex' => true),
        'South West' => array('Bath and North East Somerset' => true, 'South Gloucestershire' => true, 'North Somerset' => true, 'Bournemouth' => true, 'Poole' => true, 'Somerset' => true, 'Bristol' => true, 'Gloucestershire' => true, 'Wiltshire' => true, 'Dorset' => true, 'Devon' => true, 'Cornwall' => true),
        'London' => array('Central London and West End (WC, EC, W, SW)' => true, 'North London (N, EN)' => true, 'East London (E, IG, RM)' => true, 'South East London (SE, BR, DA, TN)' => true, 'South West London (SW, CR, KT, SM, TW)' => true, 'West London (W)' => true, 'North West London (NW, HA, UB)' => true),
    );
}
