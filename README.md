<h1>Design Cart Woo Timelines</h1>

<p>A WooCommerce product timeline: a vertical axis, horizontal branches, a date at the outer end of each branch, and small product thumbnails underneath. Built for shops that sell unique, seasonal, or handmade work — paintings, restored furniture, sculpture, books, instruments — where the year or season is part of the product, not a filter you hide in a sidebar.</p>

<p>The first store this plugin was built for is Zimowa Galeria. Paintings are made in winter, year after year. A timeline shows the growth of the work and the artist’s hand. A customer who likes a piece from one year will often like its neighbours from the same season.</p>

<h2>What you see on the front</h2>

<p>On desktop the vertical line sits in the centre. Branches alternate left and right. The date sits at the far end of the horizontal line (the line always starts at the axis). Thumbnails sit below the line — small enough that about four fit in a row. The product name is under the photo. A click goes to the product page. The pin sits on the axis, not beside it.</p>

<p>On mobile the axis moves to the left. Every branch runs left to right. Dates sit at the right-hand end.</p>

<p>Date format is also the grouping rule:</p>

<ul>
	<li><strong>Year</strong> (<code>Y</code>) — one branch per year. All 2025 pieces share one label.</li>
	<li><strong>Month</strong> (<code>F Y</code>, <code>m.Y</code>) — group by year and month.</li>
	<li><strong>Day</strong> (<code>d.m.Y</code>, <code>j F Y</code>, <code>Y-m-d</code>) — group by year, month, and day.</li>
</ul>

<p>Order can be newest first or oldest first. Product source: a hand-picked list, a WooCommerce category, or items on sale. An empty date uses the product publish date. A custom date or a text label (“Winter 2024”) overrides the formatted date for that group.</p>

<p>Thumbnail ratios: 1:1, 3:2, 2:3, 4:3, 3:4, 16:9, 4:5, 5:4, or circle. Optional image border (colour and thickness). Hover zooms the photo inside a fixed frame so neighbours do not jump. Typography is per element: heading, date, product name (link + hover colour). Background, line, pin, thicknesses, and container width are per instance.</p>

<h2>Requirements</h2>

<ul>
	<li>WordPress 6.0+</li>
	<li>PHP 7.4+</li>
	<li>WooCommerce 8.0+</li>
</ul>

<p>License: GPL-2.0-or-later.</p>

<h2>Install</h2>

<ol>
	<li>Download the plugin from the <a href="https://www.designcart.pl/laboratorium/364-jak-przedstawic-produkty-na-osi-czasu-w-woocommerce.html">project page</a> or this repository.</li>
	<li>Upload the <code>design-cart-woo-timelines</code> folder to <code>wp-content/plugins/</code>.</li>
	<li>Activate it under Plugins. WooCommerce must already be active.</li>
	<li>Open <strong>Woo Timelines</strong> in the WordPress menu, add an instance, copy the shortcode.</li>
</ol>

<h2>Shortcode</h2>

<p><code>[design_cart_woo_timelines id="1"]</code></p>

<p>Aliases: <code>[design_cart_woo_timeline id="1"]</code>, <code>[dcwt_timeline id="1"]</code>.</p>

<p><code>id</code> is the instance number from the Woo Timelines list. You can place more than one timeline on a site; each instance has its own products and CSS variables.</p>

<h2>Admin</h2>

<p>The first screen is a list of instances. Each tile shows the name, product count, and shortcode. You can add, edit, duplicate, or delete an instance. Deleting an instance does not delete WooCommerce products.</p>

<h3>General</h3>

<table>
	<thead>
		<tr>
			<th>Option</th>
			<th>What it does</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Timeline name</td>
			<td>Label in the admin list. Also the front heading if that toggle is on.</td>
		</tr>
		<tr>
			<td>Show timeline heading</td>
			<td>Renders the name as an <code>h2</code> above the axis.</td>
		</tr>
		<tr>
			<td>Source: Selected</td>
			<td>Only products from the Products tab, in list order, with your dates.</td>
		</tr>
		<tr>
			<td>Source: Category</td>
			<td>Published products from one product category, limited.</td>
		</tr>
		<tr>
			<td>Source: On sale</td>
			<td>Current sale products. Falls back to the saved list if the sale is empty.</td>
		</tr>
		<tr>
			<td>Product category</td>
			<td>Used only when the source is Category.</td>
		</tr>
		<tr>
			<td>Product limit</td>
			<td>Maximum items on the axis. Default 24.</td>
		</tr>
		<tr>
			<td>Date format</td>
			<td>Branch label and grouping (year / year+month / year+month+day).</td>
		</tr>
		<tr>
			<td>Order</td>
			<td>Newest first or oldest first.</td>
		</tr>
	</tbody>
</table>

<h3>Products</h3>

<table>
	<thead>
		<tr>
			<th>Option</th>
			<th>What it does</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Search</td>
			<td>Live WooCommerce search. Click to add.</td>
		</tr>
		<tr>
			<td>Load from source</td>
			<td>Fills the list from the General source. Keeps date and label for the same ID.</td>
		</tr>
		<tr>
			<td>Date</td>
			<td><code>YYYY-MM-DD</code>. Empty = product publish date.</td>
		</tr>
		<tr>
			<td>Date label</td>
			<td>Custom group title, e.g. “Winter 2024”. Same label = same branch.</td>
		</tr>
		<tr>
			<td>Drag handle / remove</td>
			<td>Order (Selected source) and remove from this instance only.</td>
		</tr>
	</tbody>
</table>

<h3>Appearance</h3>

<table>
	<thead>
		<tr>
			<th>Option</th>
			<th>What it does</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Image format</td>
			<td>1:1, 3:2, 2:3, 4:3, 3:4, 16:9, 4:5, 5:4, circle.</td>
		</tr>
		<tr>
			<td>Background</td>
			<td>Background of the instance wrapper (<code>#dcwt-{id}</code>).</td>
		</tr>
		<tr>
			<td>Line / node colour</td>
			<td>Vertical axis, horizontal rails, pin.</td>
		</tr>
		<tr>
			<td>Container width</td>
			<td>Axis width in px or %, centred. Default 1100 px.</td>
		</tr>
		<tr>
			<td>Line thicknesses / node size / thumb width</td>
			<td>Vertical 1–20 px, horizontal 1–20 px, pin 6–40 px, thumb 40–200 px.</td>
		</tr>
		<tr>
			<td>Image border</td>
			<td>Optional. Colour and thickness (1–20 px). Follows circle crops.</td>
		</tr>
		<tr>
			<td>Typography</td>
			<td>Heading, date, product name — font, size, unit, weight, uppercase, colour, alignment. Product name also has hover colour.</td>
		</tr>
	</tbody>
</table>

<p>The admin UI is DC Interface (Design Cart): tabs, accordion, colour picker, dimension control. Same look as the other Design Cart modules.</p>

<h2>Notes</h2>

<ul>
	<li>Hidden / non-visible catalogue products are skipped.</li>
	<li>Missing images use the WooCommerce placeholder.</li>
	<li>HPOS compatibility is declared.</li>
	<li>Uninstall removes <code>dcwt_timelines</code> and <code>dcwt_timeline_next_id</code>.</li>
</ul>

<p>Documentation (PL): <a href="https://www.designcart.pl/laboratorium/364-jak-przedstawic-produkty-na-osi-czasu-w-woocommerce.html">How to present products on a timeline in WooCommerce</a></p>

<p>Author: <a href="https://www.designcart.pl/pawel-nosko.html">Paweł Nosko</a> / Design Cart.</p>
