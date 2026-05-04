<?php

namespace Tests\Feature\Mobile\Support;

use App\Services\GuestDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InMemoryGuestDataService extends GuestDataService
{
    private array $parameters = [];
    private array $tests = [];

    public function getGuestId(Request $request): string
    {
        return $request->header('X-Guest-ID')
            ?? $request->cookie('guest_id')
            ?? Str::uuid()->toString();
    }

    public function getGuestData(string $guestId): array
    {
        return $this->parameters[$guestId] ?? [];
    }

    public function getGuestTestResults(string $guestId): array
    {
        return $this->tests[$guestId] ?? [];
    }

    public function saveGuestData(string $guestId, array $data): void
    {
        $data['updated_at'] = now()->toDateTimeString();
        $data['created_at'] ??= now()->toDateTimeString();

        $this->parameters[$guestId] = $data;
    }

    public function saveGuestTestResult(string $guestId, array $testData): void
    {
        $testData['saved_at'] = now()->toDateTimeString();
        $this->tests[$guestId] ??= [];
        $this->tests[$guestId][] = $testData;
    }

    public function updateGuestField(string $guestId, string $field, $value): array
    {
        $data = $this->getGuestData($guestId);
        $data[$field] = $value;
        $this->saveGuestData($guestId, $data);

        return $this->getGuestData($guestId);
    }

    public function updateGuestFields(string $guestId, array $fields): array
    {
        $data = array_merge($this->getGuestData($guestId), $fields);
        $this->saveGuestData($guestId, $data);

        return $this->getGuestData($guestId);
    }

    public function updateGuestTestResults(string $guestId, array $testResults): void
    {
        $this->tests[$guestId] = $testResults;
    }

    public function clearGuestTestResults(string $guestId): void
    {
        unset($this->tests[$guestId]);
    }

    public function clearGuestData(string $guestId): void
    {
        unset($this->parameters[$guestId], $this->tests[$guestId]);
    }

    public function hasGuestData(string $guestId): bool
    {
        return array_key_exists($guestId, $this->parameters);
    }

    public function hasGuestTestResults(string $guestId): bool
    {
        return array_key_exists($guestId, $this->tests);
    }
}
