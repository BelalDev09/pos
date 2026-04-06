<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository
    ) {}

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        return $this->customerRepository->paginate($perPage, $filters);
    }

    public function findById(int $id): Customer
    {
        return $this->customerRepository->findById($id);
    }

    public function searchForPos(string $term): Collection
    {
        return $this->customerRepository->searchForPos($term);
    }

    public function create(array $data): Customer
    {
        // Check for duplicate phone/email within tenant
        if (!empty($data['phone'])) {
            $existing = $this->customerRepository->findByPhone($data['phone']);
            if ($existing) {
                throw new \InvalidArgumentException(
                    "A customer with phone {$data['phone']} already exists."
                );
            }
        }

        $customer = $this->customerRepository->create($data);
        $customer->recalculateTier();

        return $customer;
    }

    public function update(int $id, array $data): Customer
    {
        $customer = $this->customerRepository->update($id, $data);
        $customer->recalculateTier();

        return $customer;
    }

    public function delete(int $id): bool
    {
        $customer = $this->customerRepository->findById($id);

        if ($customer->orders()->exists()) {
            throw new \RuntimeException(
                'Cannot delete customer with existing orders. Deactivate instead.'
            );
        }

        return $this->customerRepository->delete($id);
    }

    public function redeemLoyaltyPoints(int $customerId, float $points): Customer
    {
        $customer = $this->customerRepository->findById($customerId);

        if ($customer->loyalty_points < $points) {
            throw new \InvalidArgumentException(
                "Customer only has {$customer->loyalty_points} loyalty points."
            );
        }

        return $this->customerRepository->adjustLoyaltyPoints($customerId, $points, 'deduct');
    }
}
