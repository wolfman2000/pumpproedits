<?php $this->load->view('global/header',
  array('css' => 'css/usb.css', 'h2' => 'USB Usage', 'title' => 'USB Usage')); ?>
<p>
One of the great features about Pump It Up Pro is the integration
of common <abbr title="Universal Serial Bus">USB</abbr> drives.
These portable devices are useful for bringing business and pleasure
whenever you need it, as long as a computer was available to display the files.
</p>
<p>
When you put your USB stick into the Pump Pro arcade game, play a set,
and remove the device, you can take it to any computer and view the
contents inside. A typical drive will look similar to this:
</p>
<ul id="directory"><li>Pump It Up Pro/
<ul>
<li>Edits/<ul>
<li><var>Edit1</var>.edit</li>
<li><var>Edit2</var>.edit</li>
<li><var>Edit3</var>.edit</li>
<li><var>…</var></li>
</ul></li>
<li>LastGood/<ul><li><var>Backups of what is in the parent folder</var></li></ul></li>
<li>Screenshots/<ul>
<li><var>Screenshot1</var>.png</li>
<li><var>Screenshot2</var>.png</li>
<li><var>Screenshot3</var>.png</li>
<li><var>…</var></li>
</ul></li>
<li>Common.xsl</li>
<li>Editable.ini</li>
<li>Stats.xml.gz</li>
<li>Stats.xsl</li>
</ul>
</li></ul>

<p>Each file and folder has its purpose. Going in order, they are:</p>

<ol>
<li>Edits folder: the main reason for this website, any edits that you
download from this site or any other site go in this directory. All
edits that are created or modified through the Edit Creator are designed
to work right out of the box with no modifications for the end user: just
copy the edits into this directory once you download them.</li>
<li>LastGood folder: When you have played at least two successful games,
a copy of some of the files are placed in this directory. Should something
go wrong with a file in the main Pump It Up Pro directory, simply copy the
contents of this folder and paste it outside the folder. Note that you will
end up overriding what is there by doing so.</li>
<li>Screenshots folder: You are able to take pictures of your scores directly
while at the evaluation screen. Either hit the dedicated (red) button or
both of the arrow (yellow) buttons on the panel, and a picture of your score
will be saved. It ends up becoming a normal PNG file. Feel free to copy the PNG
out of the USB drive, but it is suggested to not rename any images in this folder.</li>
<li>Common.xsl: This is an XML based stylesheet. There is no need to view this file.</li>
<li>DontShare.sig: This file is not meant to be viewed or shared at all. It is
a safeguard file to ensure that no one steals your stats or scores.</li>
<li>Editable.ini: This file allows minor player customization. There are four entries
available for modification:
<ul>
<li>Display Name: This is the name that shows in the lower corners of
the machine when your card is in use. Based on previous experiences,
it is recommended to use no more than 12 characters for the display name.
This limit is enforced for registration purposes here at Pump Pro Edits.</li>
<li>Character ID: The purpose of this line is unknown at this time.</li>
<li>Last Used High Score Name: Once you put in your initials for getting
good scores, it will place the name you used in here. You can change it to
say whatever you want, but please be tactful.</li>
<li>Weight Pounds: For those that care about weight loss while playing, be
sure to replace the 0 here with your actual weight. Nothing bad will happen if
it is left at 0.</li>
</ul>
</li>
<li>Stats.xml: This file can be viewed in almost any web browser to
view some statistics about your playing. It is <strong>extremely</strong>
recommended to avoid editing this file by hand.</li>
<li>Stats.xml.gz: Extract this archive file to be able to view your stats.</li>
<li>Stats.xsl: This is another XML stylesheet, meant to be left alone.</li>
</ol>
<?php $this->load->view('global/footer'); ?>
