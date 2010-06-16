<?php
$scripts = array('/js/jquery-1.4.2.js', '/js/create_vars.js', '/js/create_svg.js',
  '/js/create_parse.js', '/js/create_misc.js', '/js/create_event.js', '/js/create.js');
$this->load->view('global/header',
  array('css' => 'css/create.css', 'h2' => 'Edit Creator', 'title' => 'Edit Creator',
  'andy' => $andy, 'scripts' => $scripts, 'others' => $others,
  'xhtml' => "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n<?xml-stylesheet href=\"/css/_svg.css\" type=\"text/css\"?>\r\n")); ?>
<p>Welcome to the edit creator. Use the options on the
left to place arrows below. Have fun!</p>

<svg id="svg" width="200" height="200"
  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1">
<g id="notes">
<g id="svgMeas" />
<g id="svgSect" />
<g id="svgSync" />
<g id="svgNote" />
<rect id="shadow" x="0" y="0" width="16" height="16" />
<rect id="selTop" x="0" y="0" width="80" height="16" />
<rect id="selBot" x="0" y="0" width="80" height="16" />
</g>
</svg>


<?php $this->load->view('global/footer');