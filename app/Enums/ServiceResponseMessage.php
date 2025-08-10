<?php
/* -----------------------------------------------------------------------------------
 * Copyright (c) 2024 Princeps Credit Systems Limited
 * -----------------------------------------------------------------------------------
 * This code is the property of Princeps Credit Systems Limited. Unauthorized copying,
 * sharing, or use of this code, via any medium, is strictly prohibited
 * without express permission from Princeps Credit Systems Limited.
 * -----------------------------------------------------------------------------------
 * @package    [USER SERVICE/USER]
 * @author     [COLLINS BENSON]
 * @license    Proprietary
 * @version    1.0.0
 * @link       https://www.princepscreditsystemslimited.com
 */

namespace App\Enums;


/**
 * return strings
 */
enum ServiceResponseMessage: string
{
    const CREATE_ACTION_WAS_SUCCESSFULLY = 'Create action was successfully.';
    const RECORD_DELETE = 'Deletion action was successfully.';
    const MEMBER_ADDED_SUCCESSFULLY = 'Member added successfully.';
    const RETRIEVED_DATA_SUCCESSFULLY = 'Data was retrieved Successfully.';
    const INVITATION_SENT = "Your invitation was sent successfully!.";
    const USER_DOES_NOT_EXIST = 'User does not exist.';
    const TODO_ITEM_DOES_NOT_EXIST = 'Todo list does not exist.';
    const NOT_PERMITTED = 'Sorry, you are not permitted to add to this todo list.';
    const TODO_DOES_NOT_EXIST = 'The Todo Item does not exist.';
    const CAN_NOT_ADD_MEMBER_TO_TODO_LIST = 'Can not add member to todo list, as you are not the owner of the Todo List.';
    const CAN_NOT_ADD_SELF = 'Can not add yourself as member to this Todo List.';
    const ALREADY_EXIST = 'Member already exist.';
    const DOES_NOT_EXIST = 'The selected record does not exist.';
    const DOES_NOT_BELONG = 'Sorry!, you can not add an item to the list, as you are not a member.';
    const NOT_GRANTED = 'The delete action can not be granted, as you are not the creator of the todo list.';
    const CAN_NOT_RETRIEVE_RECORD = 'No results found.';
    const UPDATE_ACTION_WAS_SUCCESSFULLY = 'Update action was successfully.';
    const REGISTRATION_SUCCESSFUL = 'Congratulations, Your account was created successfully.';
    const ERROR_OCCURRED = 'Error occurred while performing action.';
    const ACCOUNT_DOEST_NO_EXIST = 'No account exist with such email.';
    const INVALID_CREDENTIALS = 'Invalid login credentials.';
}
