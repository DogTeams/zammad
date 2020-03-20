<?php
/**
 * @package Zammad API Wrapper
 * @author  Jordan GOBLET <jordan.goblet.pro@gmail.com>
 */
namespace Dogteam\Zammad;

//use App\Http\Controller;
use ZammadAPIClient\Client;
use ZammadAPIClient\ResourceType;
use Illuminate\Support\Facades\Config;
use Dogteam\Zammad\Exception\TypeException;

class Zammad
{
    private $username;
    private $password;
    private $url;
    private $onBehalf;
    private $debug;
    private $timeout;
    private $token;

    const TICKET = 'ticket';
    const ORGANIZATION = 'organization';
    const USER = 'user';
    const GROUP = 'group';
    const TICKET_PRIORITY = 'ticket_priority';
    const TICKET_STATE = 'ticket_state';
    const TICKET_ARTICLE = 'ticket_article';

    public function __construct() {
        $this->username = config('zammad.username');
        $this->password = config('zammad.password');
        $this->url      = config('zammad.url');
        $this->token    = config('zammad.token');

    }
    /**
     * Create a Client
     * 
     * @return Client
     */
    public function client() {


        $client = new Client([
            'url'           => config('zammad.url'),       // URL of your Zammad installation
            'username'      => config('zammad.username'),  // Username to connect to Zammad
            'password'      => config('zammad.password')   // Password to connect to Zammad
        ]);
    //Define an onBehalf user if there is anyone
    if (!empty($this->onBehalf)) {
            $client->setOnBehalfOfUser($this->onBehalf);
        }

    //Unable or disable debug mode
    if ($this->debug === 'true') {
            $client->debug = true;
        }
    
    //Define timeout delay
    if (!empty($this->timeout)) {
            $client->timeout = (integer) $this->timeout;
        }
        return $client;
    }

    /**
     * Create a defined type item
     * @param String $type  Available type : ticket, user, group, organization, ticket_priority, ticket_state, ticket_article,
     * @param Array $array  Content :
     *                      Ticket content :
     *                      $ticket_data = [
     *                          'title'            => 'exemple',
     *                          'customer'         => 'exemple@exemple.exemple',
     *                          'group'            => 'exemple',
     *                          'article'          => [
     *                                  'from'         => 'exemple',
     *                                  'subject'      => 'exemple',
     *                                  'body'         => 'exemple',
     *                                  'cc'           => 'exemple1@exemple.exemple',
     *                                  'to'           => 'exemple47@exemple.exemple',
     *                                  'from'         => 'exemple2@exemple.exemple',
     *                                  'type'         => 'email',
     *                          ],
     *                      ];
     * 
     *                      User content :
     *                      $user_data = [
     *                           "id" => 1
     *                           "organization_id" => null
     *                           "login" => "user@user.com"
     *                           "firstname" => ""
     *                           "lastname" => ""
     *                           "email" => "user@user.com"
     *                      ]
     * @return void
     */
    public function create($type, $array)
    {
        try {
            switch($type) {
                case self::TICKET:
                    $this->createTicket($array);
                    break;
                case self::ORGANIZATION:
                    $this->createOrganization($array);
                    break;
                case self::TICKET_PRIORITY:
                    $this->createTicketPriority($array);
                    break;
                case self::TICKET_STATE:
                    $this->createTicketState($array);
                    break;
                case self::TICKET_ARTICLE:
                    $this->createTicketArticle($array);
                    break;
                case self::USER:
                    $this->createUser($array);
                    break;
                case self::GROUP:
                    $this->createGroup($array);
                    break;
                default:
                    throw new TypeException('not_found');
            }
        }
        catch(TypeException $e){
            return $e->getMessage();
        }
    }

    /**
     * Search for items according to defined type and keyword
     * 
     * @param String $type Available type : ticket, user, organization
     * @param String $string Keyword
     * @param String $page If there is many results pages, show the selected one
     * @param String $objects_per_page Number of results by page
     * 
     * @return void
     */
    public function search($type, $string, $page = null, $objects_per_page = null){
        try{
            switch($type) {
                case self::TICKET:
                    return $this->searchTickets($string, $page, $objects_per_page);
                    break;
                case self::USER:
                    return $this->searchUsers($string, $page, $objects_per_page);
                    break;
                case self::ORGANIZATION:
                    return $this->searchOrganizations($string, $page, $objects_per_page);
                    break;
                default:
                    throw new TypeException('not_found');
            }
        }
        catch(TypeException $e){
            return $e->getMessage();
        }
    }

    /**
     * Create a Zammad Ticket
     * 
     * @param Array $array  Ticket Content
     *                      $ticket_data = [
     *                          'title'            => 'exemple',
     *                          'customer'         => 'exemple@exemple.exemple',
     *                          'group'            => 'exemple',
     *                          'article'          => [
     *                                  'from'         => 'exemple',
     *                                  'subject'      => 'exemple',
     *                                  'body'         => 'exemple',
     *                                  'cc'           => 'exemple1@exemple.exemple',
     *                                  'to'           => 'exemple47@exemple.exemple',
     *                                  'from'         => 'exemple2@exemple.exemple',
     *                                  'type'         => 'email',
     *                          ],
     *                      ];
     */
    public function createTicket($array) {
        $ticket = $this->client()->resource(ResourceType::TICKET);
	foreach($array as $key => $value) {
            $ticket->setValue($key, $value);
        }

	$ticket->save();

        if ($ticket->hasError()) {
            return $ticket->getError();
        }
    }

    public function searchTickets($string, $page = null, $objects_per_page = null) {
        $search = $this->search = $this->client()->resource(ResourceType::TICKET)->search($string, $page, $objects_per_page);

        if ($this->search) {
            return $this->search;
        }
        if (!is_array($search)) {
            return $search->getError();
        }

        return false;
    }

    public function allTickets($page = null, $objects_per_page = null) {
        $tickets = $this->tickets = $this->client()->resource(ResourceType::TICKET)->all($page, $objects_per_page);

        if ($this->tickets) {
            return $this->tickets;
        }
        if ($tickets->hasError()) {
            return $tickets->getError();
        }

        return false;
    }
    /**
     * Find items of a defined type according to a given id
     * @param String $type Available type : ticket, organization, ticket_priority, ticket_state, ticket_article, user, group
     * @param Int $id ID number of the item
     * 
     * @return void
     */
    public function find($type, $id) {
        try{
            switch($type) {
                case self::TICKET:
                    return $this->findTicket($id);
                    break;
                case self::ORGANIZATION:
                    return $this->findOrganization($id);
                    break;
                case self::TICKET_PRIORITY:
                    return $this->findTicketPriority($id);
                    break;
                case self::TICKET_STATE:
                    return $this->findTicketState($id);
                    break;
                case self::TICKET_ARTICLE:
                    return $this->findTicketArticle($id);
                    break;
                case self::USER:
                    return $this->findUser($id);
                    break;
                case self::GROUP:
                    return $this->findGroup($id);
                    break;
                default:
                    throw new TypeException('not_found');
            }
        }
        catch(TypeException $e){
            return $e->getMessage();
        }
    }

    public function findTicket($id) {
        $ticket = $this->ticket =  $this->client()->resource(ResourceType::TICKET)->get($id);

        if ($this->ticket) {
            return $this->ticket;
        }
        if ($ticket->hasError()) {
            return $ticket->getError();
        }

        return false;
    }

    /**
     * Update a given type item with a given ID
     * @param String $type Available type : ticket, organization, ticket_priority, ticket_state, ticket_article, user, group
     * @param Int $id ID number of the item
     * @param Array $array Item content
     * 
     * @return void
     */
    public function update($type, $id, $array) {
        try{
            switch($type) {
            case self::TICKET:
                return $this->updateTicket($id, $array);
                break;
            case self::ORGANIZATION:
                return $this->updateOrganization($id, $array);
                break;
            case self::TICKET_PRIORITY:
                return $this->updateTicketPriority($id, $array);
                break;
            case self::TICKET_STATE:
                return $this->updateTicketState($id, $array);
                break;
            case self::TICKET_ARTICLE:
                return $this->updateTicketArticle($id, $array);
                break;
            case self::USER:
                return $this->updateUser($id, $array);
                break;
            case self::GROUP:
                return $this->updateGroup($id, $array);
                break;
            default:
                throw new TypeException('not_found');
            }
        }
        catch(TypeException $e){
            return $e->getMessage();
        }
    }

    public function updateTicket($id, $array) {
        $ticket = $this->client()->resource(ResourceType::TICKET)->get($id);
        foreach($array as $key => $value) {
            $ticket->setValue($key, $value);
	}

        $ticket->save();

	if ($ticket) {
            return $ticket;
    }
        if ($ticket->hasError())
        {
            return $ticket->getError();
        }
    }

    /**
     * Delete an item of a given type
     * 
     * @param String $type Available type : ticket, organization, ticket_priority, ticket_state, ticket_article, user, group
     * @param Int $id ID number of the item to delete
     * 
     * @return void
     */
    public function delete($type, $id) {
        try{
            switch($type) {
            case self::TICKET:
                return $this->deleteTicket($id);
                break;
            case self::ORGANIZATION:
                return $this->deleteOrganization($id);
                break;
            case self::TICKET_PRIORITY:
                return $this->deleteTicketPriority($id);
                break;
            case self::TICKET_STATE:
                return $this->deleteTicketState($id);
                break;
            case self::TICKET_ARTICLE:
                return $this->deleteTicketArticle($id);
                break;
            case self::USER:
                return $this->deleteUser($id);
                break;
            case self::GROUP:
                return $this->deleteGroup($id);
                break;
            default:
                throw new TypeException('not_found');
            }
        }
        catch(TypeException $e){
            return $e->getMessage();
        }
    }
    public function deleteTicket($id) {
        $ticket = $this->client()->resource(ResourceType::TICKET)->get($id);
        $ticket->delete();
    }

    public function searchUsers($string, $page = null, $objects_per_page = null) {
        $search = $this->search = $this->client()->resource(ResourceType::USER)->search($string, $page, $objects_per_page);

        if ($this->search) {
            return $this->search;
        }
        if (!is_array($search)) {
            return $search->getError();
        }

        return false;
    }

    public function allUsers($page = null, $objects_per_page = null) {
        $users = $this->users = $this->client()->resource(ResourceType::USER)->all($page, $objects_per_page);

        if ($this->users) {
            return $this->users;
        }
        if ($users->hasError()) {
            return $users->getError();
        }

        return false;
    }

    public function createUser($array) {
        $user = $this->client()->resource(ResourceType::USER);
        foreach($array as $key => $value) {
            $user->setValue($key, $value);
        }

        $user->save();

        if ($user->hasError()) {
            return $user->getError();
        }
    }

    public function findUser($id) {
        $user = $this->user =  $this->client()->resource(ResourceType::USER)->get($id);

        if ($this->user) {
            return $this->user;
        }
        if ($user->hasError()) {
            return $user->getError();
        }

        return false;
    }

    public function allGroups($page = null, $objects_per_page = null) {
        $groups = $this->groups = $this->client()->resource(ResourceType::GROUP)->all($page, $objects_per_page);

        if ($this->groups) {
            return $this->groups;
        }
        if ($groups->hasError()) {
            return $groups->getError();
        }

        return false;
    }

    public function createGroup($array) {
        $group = $this->client()->resource(ResourceType::GROUP);
        foreach($array as $key => $value) {
            $group->setValue($key, $value);
        }

        $group->save();

        if ($group->hasError()) {
            return $group->getError();
        }
    }

    public function findGroup($id) {
        $group = $this->group =  $this->client()->resource(ResourceType::GROUP)->get($id);

        if ($this->group) {
            return $this->group;
        }
        if ($group->hasError()) {
            return $group->getError();
        }

        return false;
    }

    public function updateGroup($id, $array) {
        $group = $this->client()->resource(ResourceType::GROUP)->get($id);
        foreach($array as $key => $value) {
            $group->setValue($key, $value);
        }

        $group->save();

        if ($group){
            return $group;
        }
        if ($group->hasError())
        {
            return $group->getError();
        }
    }

    public function deleteGroup($id) {
        $group = $this->client()->resource(ResourceType::GROUP)->get($id);
        $group->delete();
    }

    public function updateUser($id, $array) {
        $user = $this->client()->resource(ResourceType::USER)->get($id);
        foreach($array as $key => $value) {
            $user->setValue($key, $value);
        }

        $user->save();

        if ($user) {
            return $user;
        }
        if ($user->hasError()) {
            return $user->getError();
        }
    }

    public function deleteUser($id) {
        $user = $this->client()->resource(ResourceType::USER)->get($id);
        $user->delete();
    }

    public function searchOrganizations($string, $page = null, $objects_per_page = null) {
        $search = $this->search = $this->client()->resource(ResourceType::ORGANIZATION)->search($string, $page, $objects_per_page);

        if ($this->search){
            return $this->search;
        }
        if (!is_array($search)) {
            return $search->getError();
        }

        return false;
    }

    public function allOrganizations($page = null, $objects_per_page = null) {
        $organizations = $this->organizations = $this->client()->resource(ResourceType::ORGANIZATION)->all($page, $objects_per_page);

        if ($this->organizations) {
            return $this->organizations;
        }
        if ($organizations->hasError()) {
            return $organizations->getError();
        }

        return false;
    }

    public function createOrganization($array) {
        $organization = $this->client()->resource(ResourceType::ORGANIZATION);
        foreach($array as $key => $value) {
            $organization->setValue($key, $value);
        }

        $organization->save();

        if ($organization->hasError()) {
            return $organization->getError();
        }
    }

    public function findOrganization($id) {
        $organization = $this->organization =  $this->client()->resource(ResourceType::ORGANIZATION)->get($id);

        if ($this->organization) {
            return $this->organization;
        }
        if ($organization->hasError()) {
            return $organization->getError();
        }

        return false;
    }

    public function updateOrganization($id, $array) {
        $organization = $this->client()->resource(ResourceType::ORGANIZATION)->get($id);
        foreach($array as $key => $value) {
            $organization->setValue($key, $value);
        }

        $organization->save();

        if ($organization) {
            return $organization;
        }
        if ($organization->hasError()) {
            return $organization->getError();
        }
    }

    public function deleteOrganization($id) {
        $organization = $this->client()->resource(ResourceType::ORGANIZATION)->get($id);
        $organization->delete();
    }

    public function createTicketArticle($array) {
        $ticketArticle = $this->client()->resource(ResourceType::TICKET_ARTICLE);
        foreach($array as $key => $value) {
            $ticketArticle->setValue($key, $value);
        }

        $ticketArticle->save();

        if ($ticketArticle->hasError()) {
            return $ticketArticle->getError();
        }
    }

    public function updateTicketArticle($id, $array) {
        $ticketArticle = $this->client()->resource(ResourceType::TICKET_ARTICLE)->get($id);
        foreach($array as $key => $value) {
            $ticketArticle->setValue($key, $value);
        }

        $ticketArticle->save();

        if ($ticketArticle) {
            return $ticketArticle;
        }
        if ($ticketArticle->hasError()) {
            return $ticketArticle->getError();
        }
    }

    public function deleteTicketArticle($id) {
        $ticketArticle = $this->client()->resource(ResourceType::TICKET_ARTICLE)->get($id);
        $ticketArticle->delete();
    }

    public function allTicketStates($page = null, $objects_per_page = null) {
        $states = $this->states = $this->client()->resource(ResourceType::TICKET_STATE)->all($page, $objects_per_page);

        if ($this->states) {
            return $this->states;
        }
        if ($states->hasError()) {
            return $states->getError();
        }

        return false;
    }

    public function createTicketState($array) {
        $ticketState = $this->client()->resource(ResourceType::TICKET_STATE);
        foreach($array as $key => $value) {
            $ticketState->setValue($key, $value);
        }

        $ticketState->save();

        if ($ticketState->hasError()) {
            return $ticketState->getError();
        }
    }

    public function findTicketState($id) {
        $state = $this->state = $this->client()->resource(ResourceType::TICKET_STATE)->get($id);

        if ($this->state) {
            return $this->state;
        }
        if ($state->hasError()) {
            return $state->getError();
        }

        return false;
    }

    public function updateTicketState($id, $array) {
        $ticketState = $this->client()->resource(ResourceType::TICKET_STATE)->get($id);
        foreach($array as $key => $value){
            $ticketState->setValue($key, $value);
        }

        $ticketState->save();

        if ($ticketState) {
            return $ticketState;
        }
        if ($ticketState->hasError()) {
            return $ticketState->getError();
        }
    }

    public function deleteTicketState($id) {
        $ticketState = $this->client()->resource(ResourceType::TICKET_STATE)->get($id);
        $ticketState->delete();
    }

    public function allTicketPriorities($page = null, $objects_per_page = null) {
        $priorities = $this->priorities = $this->client()->resource(ResourceType::TICKET_PRIORITY)->all($page, $objects_per_page);

        if ($this->priorities) {
            return $this->priorities;
        }
        if ($priorities->hasError()) {
            return $priorities->getError();
        }

        return false;
    }

    public function createTicketPriority($array) {
        $ticketPriority = $this->client()->resource(ResourceType::TICKET_PRIORITY);
        foreach($array as $key => $value) {
            $ticketPriority->setValue($key, $value);
        }

        $ticketPriority->save();

        if ($ticketPriority->hasError()) {
            return $ticketPriority->getError();
        }
    }

    public function findTicketPriority($id) {
        $priority = $this->priority= $this->client()->resource(ResourceType::TICKET_PRIORITY)->get($id);

        if ($this->priority) {
            return $this->priority;
        }
        if ($priority->hasError()) {
            return $priority->getError();
        }

        return false;
    }

    public function updateTicketPriority($id, $array) {
        $ticketPriority = $this->client()->resource(ResourceType::TICKET_PRIORITY)->get($id);
        foreach($array as $key => $value) {
            $ticketPriority->setValue($key, $value);
        }

        $ticketPriority->save();

        if ($ticketPriority) {
            return $ticketPriority;
        }
        if ($ticketPriority->hasError()) {
            return $ticketPriority->getError();
        }
    }

    public function deleteTicketPriority($id) {
        $ticketPriority = $this->client()->resource(ResourceType::TICKET_PRIORITY)->get($id);
        $ticketPriority->delete();
    }

    /**
     * Display all items of a given type
     * 
     * @param $type Available type : ticket, ticket_priority, ticket_state, ticket_article, user, group, organization
     * 
     * @return void
     */
    public function all($type)
    {
        try{
            switch($type) {
             case self::TICKET:
                return $this->allTickets();
		         break;
             case self::TICKET_PRIORITY:
                return $this->allTicketPriorities();
                 break;
             case self::TICKET_STATE:
                return $this->allTicketStates();
                 break;
             case self::TICKET_ARTICLE:
                return $this->allTicketArticles();
		         break;
             case self::USER:
                return $this->allUsers();
                 break;
             case self::GROUP:
                return $this->allGroups();
                 break;
             case self::ORGANIZATION:
                return $this->allOrganizations();
                 break;
             default:
                throw new TypeException('not_found');
            }
        }
        catch(TypeException $e){
            return $e->getMessage();
        }
    }

}
