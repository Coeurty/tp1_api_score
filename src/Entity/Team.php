<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['team:read', 'player:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['team:read', 'player:read'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Player>
     */
    #[ORM\OneToMany(targetEntity: Player::class, mappedBy: 'team')]
    #[Groups(['team:read'])]
    private Collection $players;

    /**
     * @var Collection<int, Encounter>
     */
    #[ORM\OneToMany(targetEntity: Encounter::class, mappedBy: 'team1')]
    private Collection $encounters_as_team1;

    /**
     * @var Collection<int, Encounter>
     */
    #[ORM\OneToMany(targetEntity: Encounter::class, mappedBy: 'team2')]
    private Collection $encounters_as_team2;

    #[ORM\Column(nullable: true)]
    #[Groups(['team:read', 'player:read'])]
    private ?int $score = null;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->encounters_as_team1 = new ArrayCollection();
        $this->encounters_as_team2 = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): static
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setTeam($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): static
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getTeam() === $this) {
                $player->setTeam(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, Encounter>
    //  */
    // public function getEncountersAsTeam1(): Collection
    // {
    //     return $this->encounters_as_team1;
    // }

    // public function addEncountersAsTeam1(Encounter $encountersAsTeam1): static
    // {
    //     if (!$this->encounters_as_team1->contains($encountersAsTeam1)) {
    //         $this->encounters_as_team1->add($encountersAsTeam1);
    //         $encountersAsTeam1->setTeam1($this);
    //     }

    //     return $this;
    // }

    // public function removeEncountersAsTeam1(Encounter $encountersAsTeam1): static
    // {
    //     if ($this->encounters_as_team1->removeElement($encountersAsTeam1)) {
    //         // set the owning side to null (unless already changed)
    //         if ($encountersAsTeam1->getTeam1() === $this) {
    //             $encountersAsTeam1->setTeam1(null);
    //         }
    //     }

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, Encounter>
    //  */
    // public function getEncountersAsTeam2(): Collection
    // {
    //     return $this->encounters_as_team2;
    // }

    // public function addEncountersAsTeam2(Encounter $encountersAsTeam2): static
    // {
    //     if (!$this->encounters_as_team2->contains($encountersAsTeam2)) {
    //         $this->encounters_as_team2->add($encountersAsTeam2);
    //         $encountersAsTeam2->setTeam2($this);
    //     }

    //     return $this;
    // }

    // public function removeEncountersAsTeam2(Encounter $encountersAsTeam2): static
    // {
    //     if ($this->encounters_as_team2->removeElement($encountersAsTeam2)) {
    //         // set the owning side to null (unless already changed)
    //         if ($encountersAsTeam2->getTeam2() === $this) {
    //             $encountersAsTeam2->setTeam2(null);
    //         }
    //     }

    //     return $this;
    // }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

        return $this;
    }
}
