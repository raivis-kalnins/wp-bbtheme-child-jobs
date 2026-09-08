(function (wp) {
  if (!wp || !wp.blocks || !wp.element || !wp.serverSideRender) return;
  var el = wp.element.createElement;
  var __ = wp.i18n.__;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var RangeControl = wp.components.RangeControl;
  var ToggleControl = wp.components.ToggleControl;
  var ServerSideRender = wp.serverSideRender;

  function preview(name, attrs) {
    return el(ServerSideRender, { block: name, attributes: attrs });
  }
  function settingsControls(props, options) {
    var controls = [];
    if (options.perPage) controls.push(el(RangeControl, { key: 'perPage', label: __('Items to show', 'wp-bbtheme-child'), value: props.attributes.perPage, min: 1, max: 24, onChange: function(v){ props.setAttributes({perPage:v}); } }));
    if (options.columns) controls.push(el(RangeControl, { key: 'columns', label: __('Columns', 'wp-bbtheme-child'), value: props.attributes.columns, min: 1, max: options.maxColumns || 3, onChange: function(v){ props.setAttributes({columns:v}); } }));
    if (options.featured) controls.push(el(ToggleControl, { key: 'featured', label: __('Featured jobs only', 'wp-bbtheme-child'), checked: !!props.attributes.featured, onChange: function(v){ props.setAttributes({featured:v}); } }));
    if (options.compact) controls.push(el(ToggleControl, { key: 'compact', label: __('Compact search', 'wp-bbtheme-child'), checked: !!props.attributes.compact, onChange: function(v){ props.setAttributes({compact:v}); } }));
    return el(InspectorControls, {}, el(PanelBody, { title: __('HR Jobs settings', 'wp-bbtheme-child'), initialOpen: true }, controls));
  }
  function register(name, title, icon, attributes, options) {
    wp.blocks.registerBlockType(name, {
      apiVersion: 2,
      title: title,
      icon: icon,
      category: 'widgets',
      attributes: attributes,
      supports: { html: false },
      edit: function(props){ return el(wp.element.Fragment, {}, settingsControls(props, options), preview(name, props.attributes)); },
      save: function(){ return null; }
    });
  }
  register('wpbb-jobs/search', __('HR Jobs Search', 'wp-bbtheme-child'), 'search', { compact: {type:'boolean', default:false} }, {compact:true});
  register('wpbb-jobs/list', __('HR Jobs Listings', 'wp-bbtheme-child'), 'businessperson', { perPage:{type:'number',default:8}, columns:{type:'number',default:2}, featured:{type:'boolean',default:false} }, {perPage:true,columns:true,featured:true,maxColumns:3});
  register('wpbb-jobs/companies', __('HR Hiring Companies', 'wp-bbtheme-child'), 'building', { perPage:{type:'number',default:6}, columns:{type:'number',default:3} }, {perPage:true,columns:true,maxColumns:4});
  register('wpbb-jobs/resumes', __('HR Candidate Profiles', 'wp-bbtheme-child'), 'id', { perPage:{type:'number',default:6}, columns:{type:'number',default:2} }, {perPage:true,columns:true,maxColumns:3});
})(window.wp);
(function(wp){
  if (!wp || !wp.blocks || !wp.element || !wp.serverSideRender) return;
  var el=wp.element.createElement, __=wp.i18n.__, SSR=wp.serverSideRender;
  function simple(name,title,icon,attributes){
    if (wp.blocks.getBlockType(name)) return;
    wp.blocks.registerBlockType(name,{apiVersion:2,title:title,icon:icon,category:'widgets',attributes:attributes||{},supports:{html:false},edit:function(props){return el(SSR,{block:name,attributes:props.attributes});},save:function(){return null;}});
  }
  simple('wpbb-jobs/categories', __('HR Job Categories','wp-bbtheme-child'),'category',{limit:{type:'number',default:8}});
  simple('wpbb-jobs/home-metrics', __('HR Jobs Marketplace Stats','wp-bbtheme-child'),'chart-bar',{});
  simple('wpbb-jobs/salary-guide', __('HR Salary Guide','wp-bbtheme-child'),'chart-line',{limit:{type:'number',default:6}});
  simple('wpbb-jobs/salary-calculator', __('HR Salary Calculator','wp-bbtheme-child'),'calculator',{});
})(window.wp);
(function(wp){
  if (!wp || !wp.blocks || !wp.element || !wp.serverSideRender || wp.blocks.getBlockType('wpbb-jobs/career-advice')) return;
  var el=wp.element.createElement, __=wp.i18n.__, SSR=wp.serverSideRender;
  wp.blocks.registerBlockType('wpbb-jobs/career-advice',{apiVersion:2,title:__('HR Career Advice','wp-bbtheme-child'),icon:'welcome-learn-more',category:'widgets',attributes:{limit:{type:'number',default:3}},supports:{html:false},edit:function(props){return el(SSR,{block:'wpbb-jobs/career-advice',attributes:props.attributes});},save:function(){return null;}});
})(window.wp);
