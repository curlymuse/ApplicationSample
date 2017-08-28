<?php

namespace App\Events\Contracts;

interface LoggableEvent
{
    /**
     * Return the User ID
     *
     * @return int
     */
    public function getUserId();

    /**
     * @return string
     */
    public function getAccountType();

    /**
     * @return int
     */
    public function getAccountId();

    /**
     * @return string
     */
    public function getAction();

    /**
     * @return string
     */
    public function getSubjectType();

    /**
     * @return int
     */
    public function getSubjectId();

    /**
     * @return string
     */
    public function getNotes();

    /**
     * @return string
     */
    public function getDescription();
}