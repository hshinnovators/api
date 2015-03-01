<?php
/**
 * @author: KentProjects <developer@kentprojects.com>
 * @license: Copyright KentProjects
 * @link: http://kentprojects.com
 */
class Model_Project extends Model
{
	/**
	 * Get the relevant Project by it's group.
	 *
	 * @param Model_Group $group
	 * @return Model_Project
	 */
	public static function getByGroup(Model_Group $group)
	{
		if ($group->getId() === null)
		{
			return null;
		}

		$id = Cache::get($group->getCacheName("project"));
		if (empty($id))
		{
			$id = Database::prepare("SELECT `project_id` FROM `Project` WHERE `group_id` = ? AND `status` = 1", "i")
				->execute($group->getId())->singleval();
			!empty($id) && Cache::set($group->getCacheName("project"), $id, Cache::HOUR);
		}
		return !empty($id) ? static::getById($id) : null;
	}

	/**
	 * Get the relevant Project by it's ID.
	 *
	 * @param int $id
	 * @return Model_Project
	 */
	public static function getById($id)
	{
		/** @var Model_Project $project */
		$project = parent::getById($id);
		if (empty($project))
		{
			$project = Database::prepare(
				"SELECT
					`project_id` AS 'id',
					`year`,
					`group_id` AS 'group',
					`name`,
					`creator_id` AS 'creator',
					`supervisor_id` AS 'supervisor',
					`created`,
					`updated`,
					`status`
				 FROM `Project`
			 	 WHERE `project_id` = ?",
				"i", __CLASS__
			)->execute($id)->singleton();
			Cache::store($project);
		}
		return $project;
	}

	/**
	 * @var int
	 */
	protected $id;
	/**
	 * @var Model_Year
	 */
	protected $year;
	/**
	 * @var Model_Group
	 */
	protected $group;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var Model_User
	 */
	protected $creator;
	/**
	 * @var Model_User
	 */
	protected $supervisor;
	/**
	 * @var string
	 */
	protected $created;
	/**
	 * @var string
	 */
	protected $updated;
	/**
	 * @var int
	 */
	protected $status;

	public function __construct(Model_Year $year = null, $name = null, Model_User $creator = null)
	{
		if ($this->getId() !== null)
		{
			/** @noinspection PhpParamsInspection */
			$this->year = Model_Year::getById($this->year);
			/** @noinspection PhpParamsInspection */
			$this->creator = Model_User::getById($this->creator);
			/** @noinspection PhpParamsInspection */
			$this->supervisor = Model_User::getById($this->supervisor);
		}
		else
		{
			if (empty($year))
			{
				trigger_error("Missing YEAR passed to the PROJECT constructor", E_USER_ERROR);
			}
			$this->year = $year;

			if (empty($name))
			{
				trigger_error("Missing NAME passed to the PROJECT constructor", E_USER_ERROR);
			}
			$this->name = $name;

			if (empty($creator))
			{
				trigger_error("Missing CREATOR passed to the PROJECT constructor", E_USER_ERROR);
			}
			$this->creator = $creator;
		}

		parent::__construct();
	}

	/**
	 * @return string
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @return Model_User
	 */
	public function getCreator()
	{
		return $this->creator;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->metadata->description;
	}

	public function getGroup()
	{
		if (!empty($this->group) && is_numeric($this->group))
		{
			/** @noinspection PhpParamsInspection */
			$this->group = Model_Group::getById($this->group);
		}

		return $this->group;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return Model_User
	 */
	public function getSupervisor()
	{
		return $this->supervisor;
	}

	/**
	 * @return string
	 */
	public function getUpdated()
	{
		return $this->updated;
	}

	/**
	 * @return Model_Year
	 */
	public function getYear()
	{
		return $this->year;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->validateFields(array_merge(
			parent::jsonSerialize(),
			array(
				"year" => (string)$this->year,
				"group" => $this->group,
				"name" => $this->name,
				"description" => $this->getDescription(),
				"creator" => $this->creator,
				"supervisor" => $this->supervisor
			),
			$this->jsonPermissions(),
			array(
				"created" => $this->created,
				"updated" => $this->updated
			)
		));
	}

	public function save()
	{
		if (empty($this->id))
		{
			/** @var _Database_State $result */
			$result = Database::prepare(
				"INSERT INTO `Project` (`year`, `group_id`, `name`, `creator_id`, `supervisor_id`, `created`)
				 VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)",
				"iisii"
			)->execute(
				(string)$this->year, (!empty($this->group) ? $this->group->getId() : null), $this->name,
				$this->creator->getId(), $this->supervisor->getId()
			);
			$this->id = $result->insert_id;
			$this->created = $this->updated = Date::format(Date::TIMESTAMP, time());
		}
		else
		{
			if (!empty($this->group))
			{
				$group_id = ($this->group instanceof Model_Group) ? $this->group->getId() : $this->group;
			}
			else
			{
				$group_id = null;
			}
			Database::prepare(
				"UPDATE `Project`
				 SET `year` = ?, `group_id` = ?, `name` = ?, `supervisor_id` = ?
				 WHERE `project_id` = ?",
				"iisii"
			)->execute(
				(string)$this->year, $group_id, $this->name, $this->supervisor->getId(),
				$this->id
			);
			$this->updated = Date::format(Date::TIMESTAMP, time());
		}
		parent::save();
	}

	/**
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description)
	{
		$this->metadata->description = strip_tags($description);
	}

	/**
	 * @param Model_Group $group
	 * @return void
	 */
	public function setGroup(Model_Group $group)
	{
		$this->group = $group;
	}

	public function setSupervisor(Model_User $supervisor)
	{
		/**
		 * TODO: Validate this user is a registered supervisor of this year.
		 */
		$this->supervisor = $supervisor;
	}

	/**
	 * @param array $data
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function update(array $data)
	{
		if (!empty($data["description"]))
		{
			$this->setDescription($data["description"]);
		}
	}
}