<?php

/**
 * Memory Class
 * 
 * PHP Raw Network Library
 * (c) 2009 Kenneth van Hooff & Martijn Bogaard
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

class Memory {
	private $_buffer = '';
	private $_pos = 0;
	
	private $_readPos = 0;
	
	public function __construct($memorySize = 0) {
		$this->setMemorySize($memorySize);
	}
	
	public function addByte($byte) {
		$this->_buffer[$this->_pos] = $byte & 0xFF;
		$this->_pos++;
	}
	
	public function addString($string) {
		for ($i = 0; $i < strlen($string); $i++) {
			$this->addByte(ord($string[$i]));
		}
	}
	
	public function addShort($short) {
		$short &= 0xFFFF;
		
		$this->addByte($short >> 8);
		$short = $short - (($short >> 8) << 8);
		$this->addByte($short);
	}
	
	public function addInteger($int) {
		$int &= 0xFFFFFFFF;
		
		$this->addByte($int >> 24 & 0xff);
		$int = $int - (($int >> 24 & 0xff) << 24);
		$this->addByte($int >> 16 & 0xff);
		$int = $int - (($int >> 16 & 0xff ) << 16);
		$this->addByte($int >> 8 & 0xff);
		$int = $int - (($int >> 8 & 0xff) << 8);
		$this->addByte($int);
	}
	
	public function getByte() {
		return $this->_buffer[$this->_readPos++];
	}
	
	public function getShort() {
		$short = ($this->getByte() << 8);
		$short += $this->getByte();
		
		return $short;
	}
	
	public function getInteger() {		
		$int = $this->getByte() << 24;
		$int += $this->getByte() << 16;
		$int += $this->getByte() << 8;
		$int += $this->getByte();
		
		return (int)$int;
	}
	
	public function resetReadPointer() {
		$this->_readPos = 0;
	}
	
	public function setReadPointer($value) {
 		$this->_readPos = $value;
	}

	public function setByte($pos, $byte) {
		$this->_buffer[$pos] = $byte & 0xFF;
	}
	
	public function setShort($pos, $short) {
		$short &= 0xFFFF;
		
		$this->setByte($pos, $short >> 8);
		$short = $short - (($short >> 8) << 8);
		$this->setByte($pos+1, $short);
	}
	
	public function setInteger($pos, $int) {
		$int &= 0xFFFFFFFF;
		
		$this->setByte($pos, $int >> 24 & 0xff);
		$int = $int - (($int >> 24 & 0xff) << 24);
		$this->setByte($pos + 1, $int >> 16 & 0xff);
		$int = $int - (($int >> 16 & 0xff ) << 16);
		$this->setByte($pos + 2, $int >> 8 & 0xff);
		$int = $int - (($int >> 8 & 0xff) << 8);
		$this->setByte($pos + 3, $int);
	}
	
	public function setMemorySize($size) {
		if ($this->pos < $size) {
			if ($size > 0) {
				for ($i=0; $i < ($size - $this->_pos); $i++) {
					$this->addByte(0xff);
				}
			}
		}
		else {
			$this->_buffer = substr($this->_buffer, 0, $size);
			$this->_pos = $size;
		}
	}
	
	public function getMemoryLength() {
		return $this->_pos;
	}
	
	public function getMemory($startPost = 0, $endPost = -1) {
		if ($endPost == -1) {
			$endPost = $this->_pos;
		}

		$buffer = '';
		for ($i=$startPost; $i < $endPost; $i++) {
			$buffer .= chr($this->_buffer[$i]);
		}
		
		return $buffer;
	}
	
	public function dumpMemory() {
		for ($i=0; $i < $this->_pos; $i++) {
			printf("%02X ", $this->_buffer[$i]);
			
			if ((($i+1) % 50) == 0)
				printf("\n");
		}
		
		if ((($i+1) % 50) != 0)
			printf("\n");
	}
	
	public function resetMemory() {
		$this->_buffer = '';
		$this->_pos = 0;
		$this->_readPos = 0;
	}
}
?>