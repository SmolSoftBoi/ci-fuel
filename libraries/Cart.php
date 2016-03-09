<?php
/**
 * @copyright Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 * @author    Kristian Matthews <kristian.matthews@my.westminster.ac.uk>
 * @package   CodeIgniter Fuel
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cart library.
 */
class Cart implements Serializable {

	/**
	 * @var CI_Controller CodeIgniter instance.
	 */
	protected $CI;

	/**
	 * @var string Regular expression rule to validate product ID.
	 */
	private $product_id_rules = '\.a-z0-9_-'; // Alpha-numeric, dashes, underscores, or periods

	/**
	 * @var string Regular expression rule to validate product name.
	 */
	private $product_name_rules = '\.\:\-_ a-z0-9'; // Alpha-numeric, dashes, underscores, colons, or periods

	/**
	 * @var array Cart contents.
	 */
	private $cart_contents = array(
		'cart_total'  => 0,
		'items_count' => 0
	);

	/**
	 * Cart library constructor.
	 * Loads session library to store cart contents.
	 *
	 * @param array $config Configuration.
	 */
	public function __construct($config = array())
	{
		$this->CI =& get_instance();

		// Load session library
		$this->CI->load->library('session', $config);

		// Grab cart array from session, if it exists
		if ($this->CI->session->cart_contents !== FALSE)
		{
			$this->cart_contents = $this->CI->session->cart_contents;
		}

		log_message('debug', 'Cart Library Initialized');
	}

	/**
	 * Insert items into cart and save cart to session.
	 *
	 * @param array $items Items.
	 */
	public function insert($items = array())
	{
		// Check for items
		if ( ! is_array($items) || count($items) === 0)
		{
			log_message('error', 'The insert method must be passed an array containing data.');

			return FALSE;
		}

		$save_cart = FALSE;

		if (isset($items['id']))
		{
			$items = array($items);
		}

		foreach ($items as $item)
		{
			if (isset($item['id']) && isset($item['qty']))
			{
				if ($this->insert_item($item))
				{
					$save_cart = TRUE;
				}
			}
		}

		if ($save_cart)
		{
			$this->save_cart();

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Update iitems into cart and save cart to session.
	 *
	 * @param array $items Items.
	 */
	public function update($items = array())
	{
		// Check for items
		if ( ! is_array($items) || count($items) === 0)
		{
			return FALSE;
		}

		$save_cart = FALSE;

		if (isset($items['row_id']))
		{
			$items = array($items);
		}

		foreach ($items as $item)
		{
			if (isset($item['row_id']) && isset($item['qty']))
			{
				if ($this->update_item($item))
				{
					$save_cart = TRUE;
				}
			}
		}

		if ($save_cart)
		{
			$this->save_cart();

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Cart total.
	 *
	 * @return float Cart total.
	 */
	public function total()
	{
		return $this->cart_contents['cart_total'];
	}

	/**
	 * Total items.
	 *
	 * @return int Total items.
	 */
	public function items_count()
	{
		return $this->cart_contents['items_count'];
	}

	/**
	 * Cart contents.
	 *
	 * @return array Cart contents.
	 */
	public function contents()
	{
		return $this->cart_contents['items'];
	}

	/**
	 * Has options.
	 *
	 * @param string $row_id Row ID.
	 *
	 * @return bool
	 */
	public function has_options($row_id = '')
	{
		if ( ! isset($this->cart_contents['items'][$row_id]['options'])
		     || count($this->cart_contents['items'][$row_id]['options']) === 0
		)
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Product options.
	 *
	 * @param string $row_id Row ID.
	 */
	public function product_options($row_id = '')
	{
		if ( ! isset($this->cart_contents['items'][$row_id]['options']))
		{
			return array();
		}

		return $this->cart_contents['items'][$row_id]['options'];
	}

	/**
	 * Format number.
	 *
	 * @param string $n Number.
	 *
	 * @return flout Formatted number.
	 */
	public function format_number($n = '')
	{
		if ($n === '')
		{
			return '';
		}

		// Remove anything that isn't a number or decimal
		$n = trim(preg_replace('/([^0-9\.])/i', '', $n));

		return number_format($n, 2, '.', ',');
	}

	/**
	 * Destroy the cart.
	 */
	public function destroy()
	{
		unset($this->cart_contents);

		$this->cart_contents['cart_total'] = 0;
		$this->cart_contents['items_count'] = 0;

		unset($_SESSION['cart_contents']);
	}

	/**
	 * Serialize cart contents.
	 */
	public function serialize()
	{
		return serialize($this->cart_contents);
	}

	/**
	 * Unserialize cart contents.
	 */
	public function unserialize($cart_contents)
	{
		$this->cart_contents = $cart_contents;
	}

	/**
	 * Insert items into cart and save cart to session.
	 *
	 * @param array $item Item.
	 *
	 * @return mixed
	 */
	private function insert_item($item = array())
	{
		// Check for item
		if ( ! is_array($item) || count($item) === 0)
		{
			log_message('error', 'The insert method must be passed an array containing data.');

			return FALSE;
		}

		// Check for ID, quantity, price, and name
		if ( ! isset($item['id']) || ! isset($item['qty']) || ! isset($item['price']) || ! isset($item['name']))
		{
			log_message('error', 'The cart array must contain a product ID, quantity, price, and name.');

			return FALSE;
		}

		// Prep quantity
		$item['qty'] = trim(preg_replace('/([^0-9])/i', '', $item['qty']));
		// Trim leading zeros
		$item['qty'] = trim(preg_replace('/(^[0]+)/i', '', $item['qty']));

		// If quantity is zero or blank
		if ( ! is_numeric($item['qty']) || $item['qty'] === 0)
		{
			return FALSE;
		}

		// Validate product ID
		if ( ! preg_match('/^[' . $this->product_id_rules . ']+$/i', $item['id']))
		{
			log_message('error', 'An invalid name was submitted as the product name: ' . $item['name']
			                     . '. The name can only contain alpha-numberic characters, dashes, and underscores.');

			return FALSE;
		}

		// Validate product name
		if ( ! preg_match('/^[' . $this->product_name_rules . ']+$/i', $item['name']))
		{
			log_message('error', 'An invalid name was submitted as the product name: ' . $item['name']
			                     . '. The name can only contain alpha-numeric characters, dashes, underscores, colons, and spaces.');

			return FALSE;
		}

		// Prep price
		$item['price'] = trim(preg_replace('/([^0-9\.])/i', '', $item['price']));
		// Trim leading zeros
		$item['price'] = trim(preg_replace('/(^[0]+)/i', '', $item['price']));

		// If price is blank
		if ( ! is_numeric($item['price']))
		{
			log_message('error', 'An invalid price was submitted for product ID: ' . $item['id']);

			return FALSE;
		}

		// Create unique ID for item
		$options = '';
		if (isset($item['options']) && count($item['options']) > 0)
		{
			implode('', $item['options']);
		}

		$row_id = hash('sha224', $item['id'] . $options);

		// Unset duplicate row ID
		unset($this->cart_contents['items'][$row_id]);

		// Create new index with row ID
		$this->cart_contents['items'][$row_id] = $item;
		$this->cart_contents['items'][$row_id]['row_id'] = $row_id;

		return $row_id;
	}

	/**
	 * Update items into cart and save cart to session.
	 *
	 * @param array $item Item.
	 *
	 * @return bool
	 */
	private function update_item($item = array())
	{
		// Check for row ID, and quantity
		if ( ! isset($item['row_id']) || ! isset($item['qty']))
		{
			return FALSE;
		}

		// Prep quantity
		$item['qty'] = trim(preg_replace('/([^0-9])/i', '', $item['qty']));
		// Trim leading zeros
		$item['qty'] = trim(preg_replace('/(^[0]+)/i', '', $item['qty']));

		// If quantity is zero or blank
		if ( ! is_numeric($item['qty']) || $item['qty'] === 0)
		{
			return FALSE;
		}

		// Check if quantity differs
		if ($this->cart_contents['items'][$item['row_id']]['qty'] === $item['qty'])
		{
			return FALSE;
		}

		// If quantity is zero or blank
		if ( ! is_numeric($item['qty']) || $item['qty'] === 0)
		{
			unset($this->cart_contents['items'][$item['row_id']]);
		}
		else
		{
			$this->cart_contents['items'][$item['row_id']]['qty'] = $item['qty'];
		}

		return TRUE;
	}

	/**
	 * Save cart.
	 *
	 * @return bool
	 */
	private function save_cart()
	{
		// Unset cart total, and total items
		unset($this->cart_contents['cart_total']);
		unset($this->cart_contents['items_count']);

		// Add up prices and set cart sub-total
		$total = 0;
		$items = 0;

		foreach ($this->cart_contents['items'] as $row_id => $item)
		{
			$total += $item['price'] * $item['qty'];
			$items += $item['qty'];

			// Set cart sub-total
			$this->cart_contents['items'][$row_id]['sub_total'] = $this->cart_contents[$row_id]['price']
			                                                      * $this->cart_contents[$row_id]['qty'];
		}

		// Set cart total, and total items
		$this->cart_contents['cart_total'] = $total;
		$this->cart_contents['items_count'] = $items;

		// Check if cart is empty
		if (count($this->cart_contents['items']) === 0)
		{
			$this->destroy();

			return FALSE;
		}

		$_SESSION['cart_contents'] = $this->cart_contents;

		return TRUE;
	}
}