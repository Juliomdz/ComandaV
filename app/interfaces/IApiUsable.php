<?php
interface IApiUsable
{
	public function GetFirstBy($request, $response, $args);
	public function GetAll($request, $response, $args);
	public function GetAllBy($request, $response, $args);
	public function Save($request, $response, $args);
	public function Delete($request, $response, $args);
	public function Update($request, $response, $args);
}
