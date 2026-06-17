<?php
/**
 * PackNGo — Contact Message Model
 */
declare(strict_types=1);
require_once __DIR__ . '/BaseModel.php';

class ContactModel extends BaseModel
{
    protected string $table = 'contact_messages';

    public function saveMessage(array $data): int
    {
        return $this->insert([
            'name'       => strip_tags(trim($data['name'])),
            'email'      => strtolower(trim($data['email'])),
            'subject'    => isset($data['subject']) ? strip_tags(trim($data['subject'])) : null,
            'message'    => strip_tags(trim($data['message'])),
            'ip_address' => $data['ip'] ?? null,
        ]);
    }

    public function getUnread(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM contact_messages WHERE is_read = 0 ORDER BY created_at DESC"
        );
    }

    public function markRead(int $id): bool
    {
        return $this->update($id, ['is_read' => 1]) > 0;
    }

    /**
     * Store an admin reply against a message and mark it as read.
     */
    public function saveReply(int $id, string $replyMessage, ?int $repliedBy): bool
    {
        return $this->update($id, [
            'reply_message' => $replyMessage,
            'replied_at'    => date('Y-m-d H:i:s'),
            'replied_by'    => $repliedBy,
            'is_read'       => 1,
        ]) > 0;
    }
}
