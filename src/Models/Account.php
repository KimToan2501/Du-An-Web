<?php

namespace App\models;

use App\Core\Hash;
use App\Core\Model;
use App\Core\UserRole;
use Exception;

class Account extends Model
{
    // Define the table for this model
    protected static $table = 'accounts';

    protected static $primaryKey = 'user_id';

    public $user_id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $address;
    public $role;
    public $avatar_url;
    public $verify_email_at;
    public $verify_email_token;
    public $reset_password_at;
    public $reset_password_token;
    public $rating;
    public $points;
    public $ranking;
    public $created_at;
    public $updated_at;

    public function getRank($rank)
    {
        $ranks = [
            'Bronze' => 0,
            'Silver' => 5000,
            'Gold' => 10000,
            'Diamond' => 20000
        ];

        return $ranks[$rank];
    }

    public function uptoRank($points)
    {
        $ranks = [
            'Bronze' => 0,
            'Silver' => 5000,
            'Gold' => 10000,
            'Diamond' => 20000
        ];

        foreach ($ranks as $rank => $point) {
            if ($points >= $point) {
                return $rank;
            }
        }
    }

    public function getNameRanking($rank)
    {
        $ranks = [
            'Bronze' => 'Đồng',
            'Silver' => 'Bạc',
            'Gold' => 'Vàng',
            'Diamond' => 'Kim cương'
        ];

        return $ranks[$rank] ?? 'Không xác định';
    }

    public static function findByEmail($email)
    {
        return self::findOneBy('email', $email);
    }

    public static function findByPhone($phone)
    {
        return self::findOneBy('phone', $phone);
    }

    public static function findByRole($role)
    {
        return self::findOneBy('role', $role);
    }

    public static function findOrCreateAdmin()
    {
        $admin = self::findByRole(UserRole::ADMIN);

        if (!isset($admin)) {
            $newAccountAdmin = new self();
            $newAccountAdmin->name = 'Admin';
            $newAccountAdmin->email = 'admin@gmail.com';
            $newAccountAdmin->password = Hash::make('123456');
            $newAccountAdmin->role = 'admin';
            $newAccountAdmin->verify_email_at = date('Y-m-d H:i:s');
            $newAccountAdmin->verify_email_token = null;
            $newAccountAdmin->reset_password_at = null;
            $newAccountAdmin->reset_password_token = null;

            self::create([
                'name' => $newAccountAdmin->name,
                'email' => $newAccountAdmin->email,
                'password' => $newAccountAdmin->password,
                'role' => $newAccountAdmin->role,
                'verify_email_at' => $newAccountAdmin->verify_email_at,
                'verify_email_token' => $newAccountAdmin->verify_email_token,
                'reset_password_at' => $newAccountAdmin->reset_password_at,
                'reset_password_token' => $newAccountAdmin->reset_password_token,
                'rating' => 0
            ]);
        }

        return $admin;
    }

    public static function findByResetToken($token)
    {
        return self::findOneBy('reset_password_token', $token);
    }

    public function updateResetPasswordToken($token, $expiry)
    {
        $sql = "UPDATE " . static::$table . " 
            SET reset_password_token = :token, reset_password_at = :expiry 
            WHERE " . static::$primaryKey . " = :id";

        $DB = PDO();
        $stmt = $DB->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expiry', $expiry);
        $stmt->bindParam(':id', $this->{static::$primaryKey});

        return $stmt->execute();
    }

    public function updatePassword($hashedPassword)
    {
        $sql = "UPDATE " . static::$table . " 
            SET password = :password 
            WHERE " . static::$primaryKey . " = :id";

        $DB = PDO();
        $stmt = $DB->prepare($sql);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $this->{static::$primaryKey});

        return $stmt->execute();
    }

    public function clearResetPasswordToken()
    {
        $sql = "UPDATE " . static::$table . " 
            SET reset_password_token = NULL, reset_password_at = NULL 
            WHERE " . static::$primaryKey . " = :id";

        $DB = PDO();
        $stmt = $DB->prepare($sql);
        $stmt->bindParam(':id', $this->{static::$primaryKey});

        return $stmt->execute();
    }

    /**
     * Cập nhật rating trung bình của staff trong bảng accounts
     */
    public static function updateStaffRating($staffId)
    {
        try {
            $DB = PDO();

            // Tính rating trung bình của staff từ tất cả reviews
            $sql = "UPDATE accounts 
                    SET rating = (
                        SELECT ROUND(AVG(r.rating), 2)
                        FROM reviews r
                        INNER JOIN bookings b ON r.booking_id = b.id
                        WHERE b.staff_id = :staff_id
                        AND r.rating IS NOT NULL
                    ),
                    updated_at = :updated_at
                    WHERE user_id = :staff_id_update";

            $stmt = $DB->prepare($sql);
            $updatedAt = date('Y-m-d H:i:s'); // Lưu vào biến trước khi bind

            $stmt->bindParam(':staff_id', $staffId);
            $stmt->bindParam(':staff_id_update', $staffId);
            $stmt->bindParam(':updated_at', $updatedAt);

            $stmt->execute();

            // Log để debug (có thể xóa trong production)
            error_log("Updated rating for staff ID: $staffId");
        } catch (\Exception $e) {
            // Log lỗi nhưng không throw để không ảnh hưởng đến việc lưu review
            error_log("Error updating staff rating: " . $e->getMessage());
        }
    }

    /**
     * Cập nhật thông tin account theo ID
     */
    public static function updateById($id, $data)
    {
        try {
            $DB = PDO();

            // Build SET clause dynamically
            $setClause = [];
            $params = [':id' => $id];

            foreach ($data as $key => $value) {
                $setClause[] = "$key = :$key";
                $params[":$key"] = $value;
            }

            $sql = "UPDATE " . static::$table . " 
                SET " . implode(', ', $setClause) . " 
                WHERE " . static::$primaryKey . " = :id";

            $stmt = $DB->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Account update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra email đã tồn tại (trừ user hiện tại)
     */
    public static function emailExistsExcept($email, $excludeId)
    {
        try {
            $DB = PDO();
            $sql = "SELECT COUNT(*) FROM " . static::$table . " 
                WHERE email = :email AND " . static::$primaryKey . " != :exclude_id";

            $stmt = $DB->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':exclude_id', $excludeId);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log("Email exists check error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra phone đã tồn tại (trừ user hiện tại)
     */
    public static function phoneExistsExcept($phone, $excludeId)
    {
        try {
            $DB = PDO();
            $sql = "SELECT COUNT(*) FROM " . static::$table . " 
                WHERE phone = :phone AND " . static::$primaryKey . " != :exclude_id";

            $stmt = $DB->prepare($sql);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':exclude_id', $excludeId);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log("Phone exists check error: " . $e->getMessage());
            return false;
        }
    }
}
