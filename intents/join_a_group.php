<?php
/**
 * @author: KentProjects <developer@kentprojects.com>
 * @license: Copyright KentProjects
 * @link: http://kentprojects.com
 *
 * Class Intent_Join_A_Group
 * Represents someone wanting to join a group.
 */
final class Intent_Join_A_Group extends Intent
{
	/**
	 * Can this particular user create an intent of this kind?
	 *
	 * @param Model_User $user
	 * @return bool
	 */
	public function canCreate(Model_User $user)
	{
		/**
		 * If you are not a student.
		 */
		if (!$user->isStudent())
		{
			return false;
		}

		/**
		 * If you are in a group already, then fail.
		 */
		$group = Model_Group::getByUser($user);
		return empty($group);
	}

	public function canDelete(Model_User $user)
	{
		return $this->model->getUser()->getId() == $user->getId();
	}

	/**
	 * Can this particular user update this intent?
	 * In particular, is this user the creator of the group?
	 *
	 * @param Model_User $user
	 * @throws IntentException
	 * @return bool
	 */
	public function canUpdate(Model_User $user)
	{
		if (parent::canUpdate($user) === true)
		{
			return true;
		}

		if (empty($this->data->group_id))
		{
			throw new IntentException("Missing group_id.");
		}

		$group = Model_Group::getById($this->data->group_id);
		if (empty($group))
		{
			throw new IntentException("Missing group.");
		}

		/**
		 * If the group got a project before this was answered.
		 */
		if ($group->hasProject())
		{
			return false;
		}

		return $group->getCreator()->getId() == $user->getId();
	}

	/**
	 * This represents somebody who wishes to join a group.
	 *
	 * @param array $data
	 * @param Model_User $actor
	 * @throws HttpStatusException
	 * @throws IntentException
	 */
	public function create(array $data, Model_User $actor)
	{
		parent::create($data, $actor);

		if (empty($data["group_id"]))
		{
			throw new HttpStatusException(400, "Missing parameter 'group_id' for this intent.");
		}

		$group = Model_Group::getById($data["group_id"]);
		if (empty($group))
		{
			throw new HttpStatusException(404, "Group with `group_id` is not found.");
		}
		if ($group->hasProject())
		{
			throw new IntentException("This group already has a project and cannot be joined.");
		}

		$data = array_merge($data, array(
			"group_id" => $group->getId()
		));

		$this->deduplicate(array(
			"join_group" => "join_group"
		));
		$this->mergeData($data);
		$this->save();

		Notification::queue(
			"user_wants_to_join_a_group", $this->model->getUser(),
			array(
				"group_id" => $group->getId(),
				"intent_id" => $this->getId()
			),
			array("group/" . $group->getId())
		);

		$group_creator_name = $group->getCreator()->getFirstName();
		$group_name = $group->getName();
		$intent_creator_name = $this->model->getUser()->getName();

		$path = sprintf("intents.php?action=view&id=%d", $this->model->getId());

		$body = array(
			"Hey {$group_creator_name},\n\n",
			"{$intent_creator_name} wishes to join your group '{$group_name}'.\n\n",
			"To accept, please click on the relevant link:\n\n",
			"> http://localhost:5757/{$path}\n",
			"> http://localhost:8080/{$path}\n",
			"> http://dev.kentprojects.com/{$path}\n",
			"> http://kentprojects.com/{$path}\n\n",
			"Kind regards,\n",
			"Your awesome API\n\n\n",
			"For reference, here's the JSON export of the intent:\n",
			json_encode($this, JSON_PRETTY_PRINT)
		);

		/**
		 * This is where one would mail out, or at least add to a queue!
		 */
		$mail = new Postmark;
		$mail->setTo("james.dryden@kentprojects.com", "James Dryden");
		$mail->setTo("matt.house@kentprojects.com", "Matt House");
		$mail->setSubject("New Intent #" . $this->model->getId());
		$mail->setBody($body);
		// $mail->send();
	}

	/**
	 * @param Request_Internal $request
	 * @param Response $response
	 * @param ACL $acl
	 * @param boolean $internal
	 * @throws HttpStatusException
	 * @return array
	 */
	public function render(Request_Internal $request, Response &$response, ACL $acl, $internal = false)
	{
		$groupId = $this->data->group_id;
		if (empty($groupId))
		{
			throw new HttpStatusException(500, "Failed to fetch group ID for this intent.");
		}

		$group = Model_Group::getById($groupId);
		if (empty($group))
		{
			throw new HttpStatusException(500, "Failed to fetch group for this intent.");
		}

		$rendered = parent::render($request, $response, $acl, $internal);
		$rendered["group"] = $group->render($request, $response, $acl, true);
		return $rendered;
	}

	/**
	 * @param array $data
	 * @param Model_User $actor
	 * @throws IntentException
	 */
	public function update(array $data, Model_User $actor)
	{
		parent::update($data, $actor);

		if (empty($this->data->group_id))
		{
			throw new IntentException("Missing group_id.");
		}

		$group = Model_Group::getById($this->data->group_id);
		if (empty($group))
		{
			throw new IntentException("Missing group.");
		}
		if ($group->hasProject())
		{
			throw new IntentException("This group already has a project and cannot be joined.");
		}

		$this->mergeData($data);

		$group_creator_name = $group->getCreator()->getFirstName();
		$group_name = $group->getName();
		$intent_creator_name = $this->model->getUser()->getName();

		$mail = new Postmark;
		$mail->setTo("james.dryden@kentprojects.com", "James Dryden");
		$mail->setTo("matt.house@kentprojects.com", "Matt House");
		$mail->setSubject("Update Intent #" . $this->model->getId());

		switch ($this->state())
		{
			case static::STATE_OPEN:
				/**
				 * This is not the state you are looking for. Move along.
				 */
				return;
			case static::STATE_ACCEPTED:
				$students = new GroupStudentMap($group);
				$students->add($this->model->getUser());
				$students->save();

				$acl = new ACL($this->model->getUser());
				$acl->set("group", false, true, false, false);
				$acl->set("group/" . $group->getId(), false, true, true, false);
				$acl->save();

				/**
				 * Since only the group creator can manage this stuff, we can be sure the group creator is the ACTOR
				 * for this notification.
				 */
				Notification::queue(
					"user_approved_another_to_join_a_group", $group->getCreator(),
					array(
						"group_id" => $group->getId(),
						"user_id" => $this->model->getUser()->getId()
					),
					array(
						"group/" . $group->getId()
					)
				);

				$this->model->getUser()->clearCaches();

				$mail->setBody(array(
					"Hey {$intent_creator_name},\n\n",
					"{$group_creator_name} was a total lad and allowed you to join '{$group_name}'.\n",
					"Get going!\n\n",
					"Kind regards,\n",
					"Your awesome API"
				));
				// $mail->send();
				break;
			case static::STATE_REJECTED:
				Notification::queue(
					"user_rejected_another_to_join_a_group", $group->getCreator(),
					array(
						"group_id" => $group->getId(),
						"user_id" => $this->model->getUser()->getId()
					),
					array(
						"group/" . $group->getId(),
						"user/" . $this->model->getUser()->getId()
					)
				);

				$mail->setBody(array(
					"Hey {$intent_creator_name},\n\n",
					"{$group_creator_name} was a total dick and has rejected your request to join '{$group_name}'.\n",
					"Get going!\n\n",
					"Kind regards,\n",
					"Your awesome API"
				));
				// $mail->send();
				break;
			default:
				throw new IntentException("This state is not a valid Intent STATE constant.");
		}

		$this->save();
	}
}