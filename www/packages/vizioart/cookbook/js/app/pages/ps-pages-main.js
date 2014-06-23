
require.config({
	urlArgs: "noCache=" + (new Date).getTime(),
	baseUrl: CB.admin_assets_url+'/js/',
	paths: {
		'jquery': 'lib/jquery-1.11.0.min',
		'underscore': 'lib/underscore.min',
		'angular': 'lib/angular-1.2.16/angular.min',
		// angular ui
		'ui-utils': 'lib/angular-ui/ui-utils/ui-utils.min',
		'ui-bootstrap': 'lib/angular-ui/ui-bootstrap/ui-bootstrap-tpls-0.10.0.min',
		'ui-tinymce': 'lib/angular-ui/ui-tinymce/ui-tinymce',
		'ui-sortable': 'lib/angular-ui/ui-sortable/ui-sortable',
		// plugins
		'bootstrap': 'plugins/bootstrap/bootstrap.min',
		'tinymce': 'plugins/tinymce/tinymce.min',
		'datatables': 'plugins/datatables-1.9.4/media/js/jquery.dataTables.min',
		'dt-bootstrap': 'plugins/datatables-1.9.4/dt_bootstrap',
		'moment': 'plugins/moment.min',
		// "Page" specific js scripts...
	},
	shim: {
		'angular': {
			exports: 'angular'
		},
		'bootstrap': {
			deps: ['jquery'],
			exports: 'bootstrap'
		},
		'moment': {
			deps: ['jquery'],
			exports: 'moment'
		},
		'ui-utils': {
			deps: ['angular']
		},
		'ui-bootstrap': {
			deps: ['angular']
		},
		'ui-tinymce': {
			deps: ['tinymce', 'angular']
		},
		'ui-sortable': {
			deps: ['angular']
		}
	}
});


require([  
	'jquery', 
	'underscore',
	'bootstrap',
	'moment', 
	'datatables',
	'dt-bootstrap'
],function ($, _) {


	$(document).ready(function() {
		$('#cb_table_pages').dataTable({
			"sDom": "<'row'<'col-sm-12'<'pull-right'f><'pull-left'l>r<'clearfix'>>><'table-wrapper't><'row'<'col-sm-12'<'pull-left'><'dt-paging'pi><'clearfix'>>>",
			"bProcessing": true,
			"sAjaxSource": CB.admin_api_url + '/dt/pages',
			"sPaginationType": "bs_two_button",
			"iDisplayLength": 20,
			"aLengthMenu": [[20, 50, 100, -1], [25, 50, 100, "All"]],
			"aaData": [],
			"oLanguage": {
				"sInfo": "<b>_START_</b>&ndash;<b>_END_</b> of <b>_TOTAL_</b>"
			},
			"aoColumns": [
				{
					"bSortable": false,
					"sTitle": '<input type="checkbox"></input>',
					"sWidth": "20px",
					"mDataProp": null,
					"sDefaultContent": "<input type='checkbox' ></input>",
					"mData": function ( source, type, val ) {
						return '<input type="checkbox" id="someCheckbox" name="someCheckbox" />';
					}
				},
				{
					"bSortable": true,
					"sTitle": 'Title',
					"mData": function ( source, type, val ) {
						var post_id = source.id,
							post_title = '',
							edit_url = CB.admin_url + '/pages/edit/'+post_id,
							is_child = (source.parent_id > 0 ? true : false),
							html = '';


						html += '<a href="'+edit_url+'" class="post-title-link" data-post-id="'+post_id+'">';
						if (is_child){
							html += '&ndash; ';
						}
						// @change !!!
						post_title = source.post_contents[0].title || source.post_contents[1].title || source.post_contents[2].title || 'title...';
						html += post_title + '</a>';

						return html;
					}
				},
				{
					"bSortable": false,
					"sTitle": 'url',
					"mData": function ( source, type, val ) {
						var relative_url = source.post_contents[0].url,
							href = CB.site_url + relative_url;

						return '<div><a href="'+href+'" class="page_url ">'+relative_url+'</a></div>';
					},
				},
				{
					"bSortable": true,
					"sTitle": 'Date',
					"sWidth": "200px",
					"mData": function ( source, type, val ) {
						var page_id = source.id,
							status = source.status,
							created_at = moment(source.created_at),
							updated_at = moment(source.updated_at),
							now = moment(),
							old = now.diff(created_at, 'days') > 1 ? true : false,
							display = '',
							html = '';

						if (status=='publish'){

							display = old ? created_at.format("YYYY-MM-DD HH:mm") : created_at.fromNow();

							html += '<abbr title="'+created_at.utc().format()+'">'+display+'</abbr><br>';
							html += '<span class="post-status">Published</span>';
						} else {

							display = old ? updated_at.format("YYYY-MM-DD HH:mm") : updated_at.fromNow();

							html += '<abbr title="'+updated_at.utc().format()+'">'+display+'</abbr><br>';
							html += '<span class="post-status">Last modified</span>';
						}
						return html;
					},
				},
				{
					"bSortable": false,
					"sTitle": '',
					"sWidth": "160px",
					"sClass": "dt-col-actions",
					"mData": function ( source, type, val ) {
						var page_id = source.id,
							edit_url = CB.admin_url + '/pages/edit/'+page_id,
							actions_html = '';

						actions_html += '<div class="btn-row">';
						actions_html += '<a href="'+edit_url+'" class="btn-edit btn btn-default"><i class="glyphicon glyphicon-pencil"></i> Edit</a>';
						actions_html += '<a href="#" class="btn-trash btn btn-default" rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="Move Page to trash"><i class="glyphicon glyphicon-trash"></i> </a>';
						actions_html += '</div>';

						return actions_html;
					},
				},
            ]

		});

		$('.datatable').each(function(){
			var datatable = $(this);
			// SEARCH - Add the placeholder for Search and Turn this into in-line form control
			var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
			search_input.attr('placeholder', 'Search');
			search_input.addClass('form-control input-sm');
			// LENGTH - Inline-Form control
			var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
			length_sel.addClass('form-control input-sm');
		});

		$('.datatable .group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).attr("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).attr("checked", false);
                    $(this).parents('tr').removeClass("active");
                }                    
            });
        });

		$('body').tooltip({
		    selector: '[rel=tooltip]',
		    container: 'body'
		});
	} );

})