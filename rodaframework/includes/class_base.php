<?
/*
GRIDLIST
Create the html table to show data
*/
class gridList {

	public $head = array();
	public $body = array();
	public $paginate = true;
	public $havecheckbox = false;
	public $descrlist;

	function doit() {

		if($this->paginate > 0) {
			//Body
			if($this->body) {
				$bd = '<tbody>';
				foreach($this->body as $lines) {
					$bd .= '<tr>';
					foreach($lines as $values) {
						if(substr($values, 0, 3) == 'cb=') {
							$temp = explode('=', $values);
							$bd .= '<td><input type="checkbox" name="cods[]" value="'. $temp[1] .'" /></td>';
							$havecheckbox = true;
						}
						elseif(substr($values, 0, 5) == 'link=') {
							$temp = explode('=', $values);
							if((empty($temp[1])) or (empty($temp[2]))) $bd .= '<td>'. $temp[2] .'</td>';
							else {
								$bd .= '<td><span value="'. $temp[1] .'">'. $temp[2] .'</span></td>';
							}
						}
						else {
							$bd .= '<td>'. $values .'</td>';
						}
					}
					$bd .= '</tr>';
				}
				$bd .= '</tbody>';
			}
		} else {
			$bd = '<tbody><tr><td><i>Nenhum registro encontrado</i></td></tr></tbody>';
		}

		//Header
		if($this->head) {
			$hd = '<thead><tr>';
			if($havecheckbox) {
				$hd .= '<th><input type="checkbox" class="idselall" title="Inverter seleção"/></th>';
				$columnCounter++;
			}
			foreach($this->head as $name) {
				$name = str_replace('\=', '{temporaryequalchar}', $name);
				if(strpos($name, '=') !== false) {
					$divide = explode('=', $name);
					$name = $this->orderButton($divide[1]) .' '. $divide[0];
				}
				$name = str_replace('\?', '{temporaryinterrogationchar}', $name);
				if(strpos($name, '?') !== false) {
					$divide = explode('?', $name);
					$name = hintBox($divide[1]) . $divide[0];
				}
				$name = str_replace('{temporaryinterrogationchar}', '?', $name);
				$name = str_replace('{temporaryequalchar}', '=', $name);
				$hd .= '<td>'. $name .'</td>';
				$columnCounter++;
			}
			$hd .= '</tr></thead>';
		}

//		if($this->paginate) {
			$ft = '<tfoot class="paginateFooter ui-widget-footer"><tr><td colspan="'. $columnCounter .'">';
			$ft .= $this->pagination();
			$ft .= '</td></tr></tfoot>';
//		}

		$resp = '<table>';
		$resp .= $hd;
		$resp .= $bd;
		$resp .= $ft;
		$resp .= '</table>';
		return $resp;
	}

	function orderButton($v) {
		return '<span class="ui-icon ui-icon-triangle-2-n-s" style="float: left;" title="Clique para ordenar" onclick="changeOrder(\''. $v .'\')"></span>';
	}

	function pagination() {
		global $register_per_page;

		$nowpage = getFilter('nowpage');
		$registers = getFilter('registers');

		if(empty($registers)) $registers = $register_per_page;

		if($nowpage*$registers < $this->paginate) $forward = true;
		if($nowpage > 1) $back = true;

		$last = ceil($this->paginate/$registers);

		if($this->paginate) {

			if($back) {
				$first = '<a href="javascript:goToPage(1);">Início</a> | <a href="javascript:goToPage('. ($nowpage-1) .');">Anterior</a> |';
			} else {
				$first = 'Início | Anterior |';
			}

			if($forward) {
				$second = '| <a href="javascript:goToPage('. ($nowpage+1) .');">Próxima</a> | <a href="javascript:goToPage('. $last .');">Última</a>';
			} else {
				$second = '| Próxima | Última';
			}

		}

		if(is_array($this->descrlist)) {
			foreach($this->descrlist as $title => $value) if(!empty($value)) $descrfilters .= $title .': <b>'. $value .'</b>. ';
		}

		if($this->paginate) {
			$selectbox = '<span class="onRight"><span id="selectNumberRegisters"></span></span>';
			$paginateinfo = '<div class="onLeft">'. $first . '<span class="pageNumber">'. $nowpage .'/'. $last .'</span>' . $second .'</div>';
		}

		return $selectbox .'
			<div class="descrList">'. $descrfilters . $this->paginate .' registros encontrados.</div>
			'. $paginateinfo .'

			<script>
				Fields([{ id: "selectNumberRegisters", type: "select", selected: '. $registers .', atts: { style: "width: 140px;" }, options: {10:"10 registros por página", 15:"15 registros por página", 20:"20 registros por página", 50:"50 registros por página", 100:"100 registros por página", 500:"500 registros por página"}, style: "width: 30px !important;" }]);
				$("#selectNumberRegisters").change(function() { changeRegisters(this.value); })
			</script>
		';
	}
}




/*
LISTER
Get data and total number of records from DB
*/
class Lister {
	public $sql;
	public $debug = false;

	function data() {
		global $register_per_page;

		//GET DATA
		$registers = getFilter('registers');
		$nowpage   = getFilter('nowpage');
		$limitpage = $nowpage*$registers-$registers;

		if(empty($registers)) $registers = setFilter('registers', $register_per_page);
		if((empty($nowpage)) or ($nowpage < 1)) $nowpage = setFilter('nowpage', 1);

		$sqlsentence = $this->sql .' limit '. $limitpage .', '. $registers;

		if($this->debug) echo '<p>[DEBUG] SQL data: '. $sqlsentence .'</p>';

		return sql($sqlsentence);
	}

	function counter() {
		global $register_per_page;

		$this->sql = str_replace(' FROM ', ' from ', $this->sql);
		$this->sql = str_replace(' ORDER ', ' order ', $this->sql);

		//COUNTER
		$temp = explode(' from ', $this->sql);
		$temp2 = explode(' order ', $temp[1]);

		$registers = getFilter('registers');
		$nowpage = getFilter('nowpage')*$registers-$registers;
		if(empty($registers)) $registers = setFilter('registers', $register_per_page);
		if((empty($nowpage)) or ($nowpage < 1)) $nowpage = setFilter('nowpage', 1);

		$sqlsentence = 'select count(*) as total from '. $temp2[0] .' limit 1';

		if($this->debug) echo '<p>[DEBUG] SQL counter: '. $sqlsentence .'</p>';

		$cc = sql($sqlsentence);
		return $cc['total'];
	}
}
?>
