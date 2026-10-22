var config = {
	paths: {
		'jqplot': 'https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/jquery.jqplot.min',
		'jqplotBarRenderer': 'https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.barRenderer.min',
		'jqplotCategoryAxisRenderer': 'https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.categoryAxisRenderer.min',
		'jqplotPointLabels': 'https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.pointLabels.min',
		'jqplotcanvasAxisTickRenderer':'https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.canvasAxisTickRenderer.min',
		'jqplotcanvasFontRenderer':'https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.canvasTextRenderer.min',
		'jqplotcanvasAxisLabelRenderer':'https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.canvasAxisLabelRenderer.min',
		'jqplotdateAxisRenderer':'https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.dateAxisRenderer.min',
		"jqplothighlighter":'https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.highlighter.min',
		"jqChart":'Mtwo_Analytics/js/chart',
		"tableSort":'https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.30.3/js/jquery.tablesorter.min'
		
	},
	shim: {
		'tableSort': {
			deps: ['jquery']
		},
		'jqChart': {
			deps: ['jquery']
		},
		'jqplot': {
			deps: ['jquery']
		},
		'jqplotBarRenderer': {
			deps: ['jqplot']
		},
		'jqplotCategoryAxisRenderer': {
			deps: ['jqplot']
		},
		'jqplotPointLabels': {
			deps: ['jqplot']
		},
		'jqplotcanvasAxisTickRenderer': {
			deps: ['jqplot']
		},
		'jqplotcanvasFontRenderer': {
			deps: ['jqplot']
		},
		'jqplotcanvasAxisLabelRenderer': {
			deps: ['jqplot']
		},
		'jqplotdateAxisRenderer': {
			deps: ['jqplot']
		},
		'jqplothighlighter': {
			deps: ['jqplot']
		}
	}
};
