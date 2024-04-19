<?php

namespace AcademiApp\Http\Controllers;

use Illuminate\Http\Request;

use AcademiApp\User;
use AcademiApp\Modules;
use AcademiApp\Profiles;
use AcademiApp\Options;
use AcademiApp\Users_Options;
use AcademiApp\Persons;
use AcademiApp\Students;
use AcademiApp\Disabilities_Students;
use AcademiApp\Definitions;
use AcademiApp\Companies;
use AcademiApp\Tenants;
use AcademiApp\Terms_and_conditions;
use AcademiApp\User_terms_and_conditions;
use AcademiApp\Favorite_Frequent_User_Options;
use AcademiApp\ProgramsHeadquarters;
use AcademiApp\User_Program_Headquarter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use AcademiApp\Users_Profiles;
use Google_Client;
use Illuminate\Support\Facades\DB;
/**
 * @group Auth
 *
 * APIs for managing AuthController
 */
class AuthController extends Controller