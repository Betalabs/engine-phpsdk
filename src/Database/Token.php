<?php


namespace Betalabs\Engine\Database;


use Betalabs\Engine\Auth\Credentials;
use Betalabs\Engine\Auth\Token as AuthToken;
use Carbon\Carbon;

class Token
{
    /**
     * @var \PDO
     */
    private $conn;

    /**
     * Token constructor.
     */
    public function __construct()
    {
        $this->conn = Connection::get();
    }

    /**
     * @return mixed
     */
    public function first()
    {
        $identifier = Credentials::$identifier;
        $query = "SELECT * FROM tokens WHERE identifier = ?";
        $select = $this->conn->prepare($query);
        $select->bindParam(1, $identifier);
        $select->setFetchMode(\PDO::FETCH_OBJ);
        $select->execute();

        return $select->fetch();
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $identifier = Credentials::$identifier;
        $query = "DELETE FROM tokens WHERE identifier = ?";
        $delete = $this->conn->prepare($query);
        $delete->bindParam(1, $identifier);

        return $delete->execute();
    }

    /**
     * @return bool
     */
    public function insert(): bool
    {
        $identifier = Credentials::$identifier;
        $accessToken = AuthToken::getAccessToken();
        $refreshToken = AuthToken::getRefreshToken();
        $expiresAt = AuthToken::getExpiresAt();

        $lastYear = new Carbon('last year');
        if ($expiresAt < $lastYear) {
            $expiresAt = Carbon::now()->addSeconds($expiresAt->timestamp);
        }

        $seconds = $expiresAt->timestamp;
        $query = "INSERT INTO tokens VALUES (?, ?, ?, ?)";
        $insert = $this->conn->prepare($query);
        $insert->bindParam(1, $identifier);
        $insert->bindParam(2, $accessToken);
        $insert->bindParam(3, $refreshToken);
        $insert->bindParam(4, $seconds);

        return $insert->execute();
    }
}