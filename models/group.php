<?php
/**
 * @author: KentProjects <developer@kentprojects.com>
 * @license: Copyright KentProjects
 * @link: http://kentprojects.com
 */
class Model_Group extends Model
{
	/**
	 * Get the relevant Group by it's ID.
	 *
	 * @param int $id
	 * @return Model_Group
	 */
	public static function getById($id)
	{
		if (empty($id))
		{
			return null;
		}

		$statement = Database::prepare(
			"SELECT
				`group_id` AS 'id',
				`year`,
				`name`,
				`creator_id` AS 'creator',
				`created`,
				`updated`,
				`status`
			 FROM `Group`
			 WHERE `group_id` = ?",
			"i", __CLASS__
		);

		return $statement->execute($id)->singleton();
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
	 * @var string
	 */
	protected $name;
	/**
	 * @var Model_User
	 */
	protected $creator;
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

	/**
	 * @var Model_Project
	 */
	protected $project;
	/**
	 * @var GroupStudentMap
	 */
	protected $students;

	/**
	 * The reason for the @noinspection lines is because when the Database builds the model, the other 'Model' values
	 * are actually ids, and the models need fetching.
	 *
	 * @param Model_Year $year
	 * @param string $name
	 * @param Model_User $creator
	 */
	public function __construct(Model_Year $year = null, $name = null, Model_User $creator = null)
	{
		if ($this->getId() !== null)
		{
			/** @noinspection PhpParamsInspection */
			$this->year = Model_Year::getById($this->year);
			/** @noinspection PhpParamsInspection */
			$this->creator = Model_User::getById($this->creator);
		}
		else
		{
			if (empty($year))
			{
				trigger_error("Missing YEAR passed to the GROUP constructor", E_USER_ERROR);
			}
			$this->year = $year;

			if (empty($name))
			{
				trigger_error("Missing NAME passed to the GROUP constructor", E_USER_ERROR);
			}
			$this->name = $name;

			if (empty($creator))
			{
				trigger_error("Missing CREATOR passed to the GROUP constructor", E_USER_ERROR);
			}
			$this->creator = $creator;
		}

		$this->students = new GroupStudentMap($this);
	}

	/**
	 * @return string
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @return Model_User|null
	 */
	public function getCreator()
	{
		return $this->creator;
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

	public function getProject()
	{
		if (empty($this->project))
		{
			$this->project = Model_Project::getByGroup($this);
		}

		return $this->project;
	}

	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
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
				"name" => $this->name
			),
			(!empty($this->project) ? array("project" => $this->project) : array()),
			array(
				"students" => $this->students,
				"creator" => $this->creator,
				"created" => $this->created,
				"updated" => $this->updated
			)
		));
	}

	/**
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function save()
	{
		if (empty($this->id))
		{
			if (empty($this->year))
			{
				throw new InvalidArgumentException("No year has been set for the group.");
			}
			if (empty($this->name))
			{
				throw new InvalidArgumentException("No name has been set for the group.");
			}
			if (empty($this->creator))
			{
				throw new InvalidArgumentException("No creator has been set for the group.");
			}

			/** @var _Database_State $result */
			$result = Database::prepare(
				"INSERT INTO `Group` (`year`, `name`, `creator_id`, `created`)
				 VALUES (?, ?, ?, CURRENT_TIMESTAMP)",
				"isi"
			)->execute(
				(string)$this->year, $this->name, $this->creator->getId()
			);
			$this->id = $result->insert_id;
			$this->created = $this->updated = Date::format(Date::TIMESTAMP, time());
		}
		else
		{
			Database::prepare(
				"UPDATE `Group`
				 SET `name` = ?, `creator_id` = ?
				 WHERE `group_id` = ?",
				"si"
			)->execute(
				$this->name, $this->creator->getId(),
				$this->id
			);
			$this->updated = Date::format(Date::TIMESTAMP, time());
		}
		parent::save();
	}

	/**
	 * @param Model_User $user
	 * @return void
	 */
	public function setCreator(Model_User $user)
	{
		$this->creator = $user;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function setYear(Model_Year $year)
	{
		$this->year = $year;
	}
}