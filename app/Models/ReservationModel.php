<?php
/**
 * PackNGo — Reservation Model
 */

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class ReservationModel extends BaseModel
{
    protected string $table = 'reservations';

    /**
     * Create a new reservation and auto-generate a booking reference.
     */
    public function createReservation(array $data): array
    {
        return $this->db->transaction(function (Database $db) use ($data): array {

            // Generate booking ref: PNG-YYYYMMDD-XXXX
            $date = date('Ymd');
            $row  = $db->fetchOne(
                "SELECT MAX(CAST(SUBSTRING(booking_ref, 14) AS UNSIGNED)) AS seq
                   FROM reservations
                  WHERE booking_ref LIKE :pattern",
                [':pattern' => "PNG-{$date}-%"]
            );
            $seq        = ((int)($row['seq'] ?? 0)) + 1;
            $bookingRef = sprintf('PNG-%s-%04d', $date, $seq);

            $insertData = array_merge($data, ['booking_ref' => $bookingRef]);
            $id = $this->insert($insertData);

            return [
                'id'          => $id,
                'booking_ref' => $bookingRef,
            ];
        });
    }

    /**
     * Fetch a reservation by booking reference.
     */
    public function findByRef(string $ref): ?array
    {
        return $this->db->fetchOne(
            "SELECT r.*,
                    d.name  AS destination_name,
                    p.name  AS package_name,
                    p.price_usd
               FROM reservations r
          LEFT JOIN destinations d ON d.id = r.destination_id
          LEFT JOIN packages      p ON p.id = r.package_id
              WHERE r.booking_ref = :ref
              LIMIT 1",
            [':ref' => $ref]
        );
    }

    /**
     * Admin: list all reservations with optional status filter + pagination.
     */
    public function listForAdmin(int $page = 1, int $perPage = 20, string $status = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [':limit' => $perPage, ':offset' => $offset];
        $where  = '';

        if ($status !== '') {
            $where            = "WHERE r.status = :status";
            $params[':status'] = $status;
        }

        $totalRow = $this->db->fetchOne(
            "SELECT COUNT(*) AS cnt FROM reservations r {$where}",
            $status !== '' ? [':status' => $status] : []
        );

        $rows = $this->db->fetchAll(
            "SELECT r.id, r.booking_ref, r.first_name, r.last_name, r.email,
                    r.phone_code, r.phone, r.departure_date, r.return_date,
                    r.travellers, r.status, r.total_price, r.created_at,
                    d.name AS destination_name, p.name AS package_name
               FROM reservations r
          LEFT JOIN destinations d ON d.id = r.destination_id
          LEFT JOIN packages      p ON p.id = r.package_id
             {$where}
           ORDER BY r.created_at DESC
              LIMIT :limit OFFSET :offset",
            $params
        );

        $total = (int)($totalRow['cnt'] ?? 0);
        return [
            'data'         => $rows,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
        ];
    }

    /**
     * Update reservation status.
     */
    public function updateStatus(int $id, string $status, string $adminNotes = ''): bool
    {
        $allowed = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }
        $data = ['status' => $status];
        if ($adminNotes !== '') {
            $data['admin_notes'] = $adminNotes;
        }
        return $this->update($id, $data) > 0;
    }

    /**
     * Dashboard summary counts.
     */
    public function getSummary(): array
    {
        $row = $this->db->fetchOne(
            "SELECT
                COUNT(*)                                  AS total,
                SUM(status = 'pending')                   AS pending,
                SUM(status = 'confirmed')                 AS confirmed,
                SUM(status = 'cancelled')                 AS cancelled,
                SUM(status = 'completed')                 AS completed,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS today,
                COALESCE(SUM(total_price),0)              AS revenue
             FROM reservations"
        );
        return $row ?? [];
    }
}
