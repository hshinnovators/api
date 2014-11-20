<?php
/**
 * @author: KentProjects <developer@kentprojects.com>
 * @license: Copyright KentProjects
 * @link: http://kentprojects.com
 */
final class Controller_Project extends Controller
{
	/**
	 * /project
	 * /project/:id
	 *
	 * @throws HttpStatusException
	 * @return void
	 */
	public function action_index()
	{
		$this->validateMethods(Request::GET, Request::POST, Request::PUT, Request::DELETE);

		if ($this->request->getMethod() === Request::POST)
		{
			/**
			 * POST /project
			 * Used to create a project.
			 */

			if ($this->request->param("id") !== null)
			{
				throw new HttpStatusException(400, "You cannot create a project using an existing project ID.");
			}

			$params = $this->validateParams(array(
				"year" => $this->request->post("year", false),
				"name" => $this->request->post("name", false),
				"creator" => $this->request->post("creator", false)
			));

			$year = Model_Year::getById($params["year"]);
			if (empty($year))
			{
				throw new HttpStatusException(400, "Invalid year entered.");
			}

			$creator = Model_User::getById($params["creator"]);
			if (empty($creator))
			{
				throw new HttpStatusException(400, "Invalid user id entered for the project's creator.");
			}

			$slug = slugify($params["name"]);

			if (!Model_Project::validate($year, $slug))
			{
				throw new HttpStatusException(400, "This year already has a project with that name '" . $params["name"] . "'.");
			}

			$project = new Model_Project($year, $params["name"], $slug, $creator);
			$project->save();

			$this->response->status(201);
			$this->response->body($project);
			return;
		}

		if ($this->request->param("id") === null)
		{
			throw new HttpStatusException(400, "No project id provided.");
		}

		$project = Model_Project::getById($this->request->param("id"));
		if (empty($project))
		{
			throw new HttpStatusException(404, "Project not found.");
		}

		if ($this->request->getMethod() === Request::PUT)
		{
			/**
			 * PUT /project/:id
			 * Used to update a project!
			 */
			throw new HttpStatusException(501, "Updating a project is coming soon.");
		}
		elseif ($this->request->getMethod() === Request::DELETE)
		{
			/**
			 * DELETE /project/:id
			 * Used to delete a project.
			 */
			throw new HttpStatusException(501, "Deleting a project is coming soon.");
		}

		/**
		 * GET /project/:id
		 * Used to get a project.
		 */

		$this->response->status(200);
		$this->response->body($project);
	}

	/**
	 * /project/group
	 * /project/:id/group
	 *
	 * @throws HttpStatusException
	 * @return void
	 */
	public function action_group()
	{
		$this->validateMethods(Request::POST);

		/**
		 * POST /project/:id/group
		 */

		if ($this->request->param("id") === null)
		{
			throw new HttpStatusException(400, "No project id provided.");
		}

		$project = Model_Project::getById($this->request->param("id"));
		if (empty($project))
		{
			throw new HttpStatusException(404, "Project not found.");
		}

		throw new HttpStatusException(501, "Adding a group to a project is coming soon.");
	}

	/**
	 * /project/rollover
	 * /project/:id/rollover
	 *
	 * @throws HttpStatusException
	 * @return void
	 */
	public function action_rollover()
	{
		$this->validateMethods(Request::POST);

		/**
		 * POST /project/:id/rollover
		 */

		if ($this->request->param("id") === null)
		{
			throw new HttpStatusException(400, "No project id provided.");
		}

		$project = Model_Project::getById($this->request->param("id"));
		if (empty($project))
		{
			throw new HttpStatusException(404, "Project not found.");
		}

		throw new HttpStatusException(501, "Rolling over a project is coming soon.");
	}
}