<?php
class ModelCatalogProfile extends Model {
	public function getFrequencies() {
		return array(
			'day' => $this->language->get('text_day'),
			'week' => $this->language->get('text_week'),
			'semi_month' => $this->language->get('text_semi_month'),
			'month' => $this->language->get('text_month'),
			'year' => $this->language->get('text_year'),
		);
	}

	public function getProfile($profile_id) {
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "profile` WHERE profile_id = " . (int)$profile_id)->row;
	}

	public function getProfileDescription($profile_id) {
		$profile_descriptions = array();

		$results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "profile_description` WHERE `profile_id` = " . (int)$profile_id . "
		")->rows;

		foreach ($results as $result) {
			$profile_descriptions[$result['language_id']] = array(
				'name' => $result['name'],
			);
		}

		return $profile_descriptions;
	}

	public function getProfiles($data = array()) {
		$sql = "SELECT p.profile_id, p.sort_order, pd.name FROM " . DB_PREFIX . "profile AS p LEFT JOIN " . DB_PREFIX . "profile_description pd ON (pd.profile_id = p.profile_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'p.sort_order',
			'pd.name'
		);	

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY p.sort_order";	
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}																																							  

		$query = $this->db->query($sql);																																				

		return $query->rows;	
	}

	public function getTotalProfiles() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "profile");

		return $query->row['total'];
	}

	public function addProfile($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "profile` SET `sort_order` = " . (int)$data['sort_order'] . ", `status` = " . (int)$data['status'] . ", `price` = " . (double)$data['price'] . ", `frequency` = '" . $this->db->escape($data['frequency']) . "', `duration` = " . (int)$data['duration'] . ", `cycle` = " . (int)$data['cycle'] . ", `trial_status` = " . (int)$data['trial_status'] . ", `trial_price` = " . (double)$data['trial_price'] . ", `trial_frequency` = '" . $this->db->escape($data['trial_frequency']) . "', `trial_duration` = " . (int)$data['trial_duration'] . ", `trial_cycle` = " . (int)$data['trial_cycle']);

		$profile_id = $this->db->getLastId();

		foreach ($data['profile_description'] as $language_id => $profile_description) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "profile_description` (`profile_id`, `language_id`, `name`) VALUES (" . (int)$profile_id . ", " . (int)$language_id. ", '" . $this->db->escape($profile_description['name']) . "')");
		}

		return $profile_id;
	}

	public function updateProfile($profile_id, $data) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "profile_description` WHERE profile_id = " . (int)$profile_id);

		$this->db->query("UPDATE `" . DB_PREFIX . "profile` SET `sort_order` = " . (int)$data['sort_order'] . ", `status` = " . (int)$data['status'] . ", `price` = " . (double)$data['price'] . ", `frequency` = '" . $this->db->escape($data['frequency']) . "', `duration` = " . (int)$data['duration'] . ", `cycle` = " . (int)$data['cycle'] . ", `trial_status` = " . (int)$data['trial_status'] . ", `trial_price` = " . (double)$data['trial_price'] . ", `trial_frequency` = '" . $this->db->escape($data['trial_frequency']) . "', `trial_duration` = " . (int)$data['trial_duration'] . ", `trial_cycle` = " . (int)$data['trial_cycle'] . " WHERE profile_id = " . (int)$profile_id);

		foreach ($data['profile_description'] as $language_id => $profile_description) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "profile_description` (`profile_id`, `language_id`, `name`) VALUES (" . (int)$profile_id . ", " . (int)$language_id. ", '" . $this->db->escape($profile_description['name']) . "')");
		}
	}

	public function deleteProfile($profile_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "profile` WHERE profile_id = " . (int)$profile_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "profile_description` WHERE profile_id = " . (int)$profile_id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_profile` WHERE profile_id = " . (int)$profile_id);

		$this->db->query("UPDATE `" . DB_PREFIX . "order_recurring` SET `profile_id` = 0 WHERE `profile_id` = " . (int)$profile_id);
	}

	public function copyProfile($profile_id) {
		$data = $this->getProfile($profile_id);
		$data['profile_description'] = $this->getProfileDescription($profile_id);

		foreach ($data['profile_description'] as &$profile_description) {
			$profile_description['name'] .= ' - 2';
		}

		$this->addProfile($data);
	}
}
?>