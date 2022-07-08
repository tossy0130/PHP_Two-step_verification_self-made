<?php

namespace Matrix\Decomposition;

use Matrix\Matrix;

class QR
{
    private $qrMatrix;
    private $rows;
    private $columns;

    private $rDiagonal = [];

    public function __construct(Matrix $matrix)
    {
        $this->qrMatrix = $matrix->toArray();
        $this->rows = $matrix->rows;
        $this->columns = $matrix->columns;

        $this->decompose();
    }

    public function getHouseholdVectors()
    {
        $householdVectors = [];
        for ($row = 0; $row < $this->rows; ++$row) {
            for ($column = 0; $column < $this->columns; ++$column) {
                if ($row >= $column) {
                    $householdVectors[$row][$column] = $this->qrMatrix[$row][$column];
                } else {
                    $householdVectors[$row][$column] = 0.0;
                }
            }
        }

        return new Matrix($householdVectors);
    }

    public function getQ()
    {
        $qGrid = [];

        $rowCount = $this->rows;
        for ($k = $this->columns - 1; $k >= 0; --$k) {
            for ($i = 0; $i < $this->rows; ++$i) {
                $qGrid[$i][$k] = 0.0;
            }
            $qGrid[$k][$k] = 1.0;
            if ($this->columns > $this->rows) {
                $qGrid = array_slice($qGrid, 0, $this->rows);
            }

            for ($j = $k; $j < $this->columns; ++$j) {
                if (isset($this->qrMatrix[$k], $this->qrMatrix[$k][$k]) && $this->qrMatrix[$k][$k] != 0.0) {
                    $s = 0.0;
                    for ($i = $k; $i < $this->rows; ++$i) {
                        $s += $this->qrMatrix[$i][$k] * $qGrid[$i][$j];
                    }
                    $s = -$s / $this->qrMatrix[$k][$k];
                    for ($i = $k; $i < $this->rows; ++$i) {
                        $qGrid[$i][$j] += $s * $this->qrMatrix[$i][$k];
                    }
                }
            }
        }

        array_walk(
            $qGrid,
            function (&$row) use ($rowCount) {
                $row = array_reverse($row);
                $row = array_slice($row, 0, $rowCount);
            }
        );

        return new Matrix($qGrid);
    }

    public function getR()
    {
        $rGrid = [];

        for ($row = 0; $row < $this->columns; ++$row) {
            for ($column = 0; $column < $this->columns; ++$column) {
                if ($row < $column) {
                    $rGrid[$row][$column] = isset($this->qrMatrix[$row][$column]) ? $this->qrMatrix[$row][$column] : 0.0;
                } elseif ($row === $column) {
                    $rGrid[$row][$column] = isset($this->rDiagonal[$row]) ? $this->rDiagonal[$row] : 0.0;
                } else {
                    $rGrid[$row][$column] = 0.0;
                }
            }
        }

        if ($this->columns > $this->rows) {
            $rGrid = array_slice($rGrid, 0, $this->rows);
        }

        return new Matrix($rGrid);
    }

    private function hypo($a, $b)
    {
        if (abs($a) > abs($b)) {
            $r = $b / $a;
            $r = abs($a) * sqrt(1 + $r * $r);
        } elseif ($b != 0.0) {
            $r = $a / $b;
            $r = abs($b) * sqrt(1 + $r * $r);
        } else {
            $r = 0.0;
        }

        return $r;
    }

    /**
     * QR Decomposition computed by Householder reflections.
     */
    private function decompose()
    {
        for ($k = 0; $k < $this->columns; ++$k) {
            // Compute 2-norm of k-th column without under/overflow.
            $norm = 0.0;
            for ($i = $k; $i < $this->rows; ++$i) {
                $norm = $this->hypo($norm, $this->qrMatrix[$i][$k]);
            }
            if ($norm != 0.0) {
                // Form k-th Householder vector.
                if ($this->qrMatrix[$k][$k] < 0.0) {
                    $norm = -$norm;
                }
                for ($i = $k; $i < $this->rows; ++$i) {
                    $this->qrMatrix[$i][$k] /= $norm;
                }
                $this->qrMatrix[$k][$k] += 1.0;
                // Apply transformation to remaining columns.
                for ($j = $k + 1; $j < $this->columns; ++$j) {
                    $s = 0.0;
                    for ($i = $k; $i < $this->rows; ++$i) {
                        $s += $this->qrMatrix[$i][$k] * $this->qrMatrix[$i][$j];
                    }
                    $s = -$s / $this->qrMatrix[$k][$k];
                    for ($i = $k; $i < $this->rows; ++$i) {
                        $this->qrMatrix[$i][$j] += $s * $this->qrMatrix[$i][$k];
                    }
                }
            }
            $this->rDiagonal[$k] = -$norm;
        }
    }
}
