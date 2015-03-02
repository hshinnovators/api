<?php
/**
 * @author: KentProjects <developer@kentprojects.com>
 * @license: Copyright KentProjects
 * @link: http://kentprojects.com
 */
final class Controller_Year extends Controller
{
	/**
	 * /year
	 * /year/:id
	 *
	 * @throws HttpStatusException
	 * @return void
	 */
	public function action_index()
	{
		$this->validateMethods(Request::GET, Request::POST);

		if ($this->request->getMethod() === Request::POST)
		{
			/**
			 * POST /year
			 * Used to create a new year!
			 * Happy new year! ^_^
			 */
			/**
			 * TODO: WRITE THIS.
			 *
			 * Is user a convener for this year?
			 * Create a new academic year.
			 * Set yourself as convener.
			 *
			 * SQL for when Future JD forgets:
			 * INSERT INTO `Year` (`year`) VALUES (DEFAULT(`year`));
			 */
			throw new HttpStatusException(501, "Creating a new year is coming soon.");
		}

		if ($this->request->param("id") === null)
		{
			throw new HttpStatusException(400, "No year provided.");
		}

		$year = Model_Year::getById($this->request->param("id"));
		if (empty($year))
		{
			throw new HttpStatusException(404, "Year not found.");
		}

		/**
		 * GET /year/:id
		 * Get a year.
		 */

		$this->response->status(200);
		$this->response->body($year);
	}

	/**
	 * /year/:id/stats
	 *
	 * @throws HttpStatusException
	 * @return void
	 */
	public function action_stats()
	{
		$this->validateMethods(Request::GET);

		if ($this->request->param("id") === null)
		{
			throw new HttpStatusException(400, "No year provided.");
		}
		$year = Model_Year::getById($this->request->param("id"));
		if (empty($year))
		{
			throw new HttpStatusException(404, "Year not found.");
		}

		$this->response->status(200);
		$this->response->body(Model_Stats::getForYear($year));
	}
}