GET adlog/_search
{
    "size": 0,
    "_source": {
        "excludes": []
    },
    "aggs": {
        "reports": {
            "date_histogram": {
                "field": "add_time",
                "order": [
                    {
                        "_term": "desc"
                    }
                ],
                "interval": "day",
                "format": "yyyy-MM-dd",
                "time_zone": "+08:00",
                "min_doc_count": 0,
                "extended_bounds": {
                    "min": "2018-08-05",
                    "max": "2018-08-28"
                }
            },
            "aggs": {
                "distinct_ip_count": {
                    "cardinality": {
                        "field": "from_ip.keyword"
                    }
                },
                "ip_count": {
                    "value_count": {
                        "field": "from_ip.keyword"
                    }
                },
                "load_count": {
                    "filter": {
                        "term": {
                            "status": "1"
                        }
                    }
                },
                "click_count": {
                    "filter": {
                        "term": {
                            "click_status": "1"
                        }
                    }
                },
                "down_count": {
                    "filter": {
                        "term": {
                            "down_status": "1"
                        }
                    }
                },
                "ad_reports": {
                    "terms": {
                        "field": "ad_id.keyword"
                    },
                    "aggs": {
                        "sub_promote_id": {
                            "terms": {
                                "field": "promote_id.keyword"
                            }
                        },
                        "distinct_ip_count": {
                            "cardinality": {
                                "field": "from_ip.keyword"
                            }
                        },
                        "ip_count": {
                            "value_count": {
                                "field": "from_ip.keyword"
                            }
                        },
                        "load_count": {
                            "filter": {
                                "term": {
                                    "status": "1"
                                }
                            }
                        },
                        "click_count": {
                            "filter": {
                                "term": {
                                    "click_status": "1"
                                }
                            }
                        },
                        "down_count": {
                            "filter": {
                                "term": {
                                    "down_status": "1"
                                }
                            }
                        }
                    }
                }
            }
        }
    },
    "query": {
        "bool": {
            "must": [
                {
                    "range": {
                        "add_time": {
                            "gt": "2018-08-05",
                            "lte": "2018-08-28",
                            "time_zone": "+08:00"
                        }
                    }
                }
            ],
            "should": [],
            "must_not": []
        }
    }
}

GET adlog/_search
{
    "size": 0,
    "_source": {
        "excludes": []
    },
    "aggs": {
        "reports": {
            "date_histogram": {
                "field": "@timestamp",
                "order": [{
                    "_term": "desc"
                }],
                "interval": "day",
                "format": "yyyy-MM-dd",
                "time_zone": "+08:00",
                "min_doc_count" : 0,
                "extended_bounds" : {
                    "min" : "2018-08-05",
                    "max" : "2018-08-28"
                }
            },
            "aggs": {
                "ip_count": {
                    "value_count": {
                        "field": "from_ip.keyword"
                    }
                },
                "load_count": {
                    "filter": {
                        "term": {
                            "status": "1"
                        }
                    }
                },
                "click_count": {
                    "value_count": {
                        "field": "click_status.keyword"
                    }
                },
                "down_count": {
                    "filter": {
                        "term": {
                            "down_status": "1"
                        }
                    }
                },
                "ad_reports": {
                    "terms": {
                        "script": {
                            "inline": "doc['ad_id.keyword'].value +'-'+ doc['promote_id.keyword'].value +'-'+ doc['sub_promote_id.keyword'].value"
                        }
                    },
                    "aggs": {
                        "ip_count": {
                            "value_count": {
                                "field": "from_ip.keyword"
                            }
                        },
                        "load_count": {
                            "filter": {
                                "term": {
                                    "status": "1"
                                }
                            }
                        },
                        "click_count": {
                            "value_count": {
                                "field": "click_status.keyword"
                            }
                        },
                        "down_count": {
                            "filter": {
                                "term": {
                                    "down_status": "1"
                                }
                            }
                        }
                    }
                }
            }
        }
    },
    "query": {
        "bool": {
            "must": [{
                "range": {
                    "@timestamp": {
                        "gt": "2018-08-05",
                        "lte": "2018-08-28",
                        "time_zone": "+08:00"
                    }
                }
            }],
            "should": [],
            "must_not": []
        }
    }
}

// 统计
GET adlog/_search
{
    "size": 0,
    "_source": {
        "excludes": []
    },
    "aggs": {
        "ip_count": {
            "value_count": {
                "field": "from_ip.keyword"
            }
        },
        "load_count": {
            "filter": {
                "term": {
                    "status": "1"
                }
            }
        },
        "click_count": {
            "value_count": {
                "field": "click_status.keyword"
            }
        },
        "down_count": {
            "filter": {
                "term": {
                    "down_status": "1"
                }
            }
        },
        "ad_ids": {
            "terms": {
                "field": "ad_id.keyword"
            }
        }
    },
    "query": {
        "bool": {
            "must": [
                {
                    "range": {
                        "@timestamp": {
                            "gte": "2018-08-28",
                            "lte": "2018-08-28",
                            "time_zone": "+08:00"
                        }
                    }
                }
            ],
            "should": [],
            "must_not": []
        }
    }
}

GET adlog/_search
{
    "from": 0,
    "size": 1,
    "_source": {
        "includes": [
            "add_time",
            "@timestamp"
        ]
    },
    "sort": [
        {
            "@timestamp": "asc"
        }
    ]
}

GET adlog/_search
{
    "from": 0,
    "size": 1,
    "_source": {
        "includes": [
            "add_time",
            "@timestamp"
        ]
    },
    "sort": [
        {
            "add_time": "asc"
        }
    ]
}

GET adlog/_search
{
    "from": 0,
    "size": 1,
    "_source": {
        "includes": [
            "add_time",
            "@timestamp"
        ]
    },
    "docvalue_fields": [
        "@timestamp",
        "add_date",
        "add_time"
    ],
    "sort": [
        {
            "add_time": "asc"
        }
    ]
}

GET adlog/_search
{
    "size": 0,
    "aggs": {
        "my_buckets": {
            "composite": {
                "size": 999,
                "sources": [
                    {
                        "date": {
                            "date_histogram": {
                                "field": "add_time",
                                "interval": "day",
                                "order": "desc",
                                "format": "yyyy-MM-dd",
                                "time_zone": "+08:00"
                            }
                        }
                    },
                    {
                        "ad_id": {
                            "terms": {
                                "field": "ad_id.keyword"
                            }
                        }
                    },
                    {
                        "media_id": {
                            "terms": {
                                "field": "media_id.keyword"
                            }
                        }
                    },
                    {
                        "promote_id": {
                            "terms": {
                                "field": "promote_id.keyword"
                            }
                        }
                    },
                    {
                        "sub_promote_id": {
                            "terms": {
                                "field": "sub_promote_id.keyword"
                            }
                        }
                    }
                ]
            },
            "aggs": {
                "ip_count": {
                    "value_count": {
                        "field": "from_ip.keyword"
                    }
                },
                "load_count": {
                    "filter": {
                        "term": {
                            "status": "1"
                        }
                    }
                },
                "click_count": {
                    "value_count": {
                        "field": "click_status.keyword"
                    }
                },
                "down_count": {
                    "filter": {
                        "term": {
                            "down_status": "1"
                        }
                    }
                },
                "ad_reports": {
                    "terms": {
                        "field": "ad_id.keyword"
                    },
                    "aggs": {
                        "ip_count": {
                            "value_count": {
                                "field": "from_ip.keyword"
                            }
                        },
                        "load_count": {
                            "filter": {
                                "term": {
                                    "status": "1"
                                }
                            }
                        },
                        "click_count": {
                            "value_count": {
                                "field": "click_status.keyword"
                            }
                        },
                        "down_count": {
                            "filter": {
                                "term": {
                                    "down_status": "1"
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}