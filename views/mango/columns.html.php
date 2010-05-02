<h1>Mango - Column Definition</h1>

<h2>Columns</h2>

<h3>MongoID</h3>

<table border=1>
	<tr>
		<td>MongoID</td>
		<td>A MongoID object</td>
		<td><pre>'_id' => array('type'=>'MongoId')</pre></td>
	</tr>
</table>

<h3>Numeric types</h3>
Numeric types support the optional ‘min_value’ and ‘max_value’ setting to indicate the minimum and maximum allowed value of the column when validated.

<table border=1>
	<tr>
		<td>int</td>
		<td>An integer</td>
		<td><pre>'age' => array('type'=>'int','min_value'=>0)</pre></td>
	</tr>
	<tr>
		<td>float</td>
		<td>A float</td>
		<td><pre>'average' => array('type'=>'float')</pre></td>
	</tr>
</table>

<h3>String types</h3>

<table border=1>
	<tr>
		<td>Enum</td>
		<td>A string type where its possible values are predefined (in the 'values' key). The (numeric) key of the string value in the values array is saved in the DB. The 'values' key is also used when validating (if the value is not in the values array, validation won't pass)</td>
		<td><pre>'size' => array('type'=>'enum','values'=>array('small','medium','large'))</pre></td></td>
	</tr>
	<tr>
		<td>String</td>
		<td>A string value, with optional min_length and max_length settings, that are (like with numeric values) used during validation</td>
		<td><pre>'username' => array('type'=>'string','min_length'=>3,'max_length'=>10)</pre></td>
	</tr>
	<tr>
		<td>Email</td>
		<td>Requires a (valid) emailaddress</td>
		<td><pre>'email' => array('type'=>'email')</pre></td>
	</tr>
</table>

<h3> Boolean </h3>
<table border=1>
	<tr>
		<td>boolean</td>
		<td>A boolean</td>
		<td><pre>'average' => array('type'=>'boolean')</pre></td>
	</tr>
</table>

<h3> Embedded Objects </h3>
<table border=1>
	<tr>
		<td>has_one</td>
		<td>An embedded mango object - column name should correspond the object_name or use the 'model' key to specify a model name</td>
		<td><pre>'account' => array('type'=>'has_one')</pre></td>
	</tr>
	<tr>
		<td>has_many</td>
		<td>An embedded set of mango objects - column name should be the embedded object(s) object_name in plural, or use the 'model' key to specify a model name (in plural)</td>
		<td><pre>'comments' => array('type'=>'has_many')</pre></td>
	</tr>
</table>

<h3> Counters </h3>
<table border=1>
	<tr>
		<td>counter</td>
		<td>A numeric value that supports the ->increment($value) and ->decrement($value) methods</td>
		<td><pre>'views' => array('type'=>'counter')</pre></td>
	</tr>
</table>

<h3> (Associative) Arrays and Sets </h3>
Please note the naming difference in comparison to MongoDB - PHP (associative) arrays are morel ike MongoDB objects, and 'sets' (non associative arrays) are more like arrays in MongoDB.<br>

When dealing with (changes in) multidimensional arrays, be sure to set the type_hint key, to 'array' or 'set'. This way, embedded changes are correctly updated. If you don't use embedded changes, you don't have to set the type_hint.<br>
EG:<br>
<pre>$object->array = array( 'a' => array('b' => 'c') );
$object->save();</pre>
This is always supported (with and without type_hint):<br>
<pre>$object->array['d'] = array('e' => 'f');</pre>
This is only supported when the type_hint is set to 'array':<br>
<pre>$object->array['a']['g'] = 'h';</pre>

Other values for type_hint can be 'counter' (supports a (multidimensional) array of counters) and a object_name (will load Mango Objects).

<table border=1>
	<tr>
		<td>array</td>
		<td>Corresponds to a MongoDB object</td>
		<td><pre>'views' => array('type'=>'array')</pre></td>
	</tr>
	<tr>
		<td>set</td>
		<td>Corresponds to a MongoDB array. This type has a separate attribute 'unique'. If set (to TRUE) it will take care that only unique values are added to the set</td>
		<td><pre>'views' => array('type'=>'set', 'unique' => TRUE)</pre></td>
	</tr>
</table>

<h3>Mixed</h3>
<table border=1>
	<tr>
		<td>mixed</td>
		<td>Accepts anything (string/integers/arrays) except objects</td>
		<td><pre>'data' => array('type'=>'mixed')</pre></td>
	</tr>
</table>

<h3>Additional column settings</h3>
These settings can be added to each column type
<table border=1>
	<tr>
		<td>required</td>
		<td>Make an value required (when validating)</td>
		<td><pre>'password' => array('type'=>'string','required'=>TRUE)</pre></td>
	</tr>
	<tr>
		<td>default</td>
		<td>Set a default value that is returned when column is not set</td>
		<td><pre>'days' => array('type'=>'int','default'=>7)</pre></td>
	</tr>
	<tr>
		<td>local</td>
		<td>Make a column local; that is - it is never stored in the database</td>
		<td><pre>'message' => array('type'=>'string','local'=>TRUE)</pre></td>
	</tr>
</table>